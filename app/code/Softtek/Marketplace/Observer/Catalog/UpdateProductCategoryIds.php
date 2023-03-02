<?php
namespace Softtek\Marketplace\Observer\Catalog;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\CategoryLinkInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\EntityMetadataInterface;

/**
 * Product Save After Observer Model
 */
class UpdateProductCategoryIds implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CategoryLinkManagementInterface
     */
    protected $categoryLinkManagement;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var EntityMetadataInterface
     */
    protected $categoryLinkMetadata;

    /**
     * Observer constructor.
     *
     * @param RequestInterface $request
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        RequestInterface $request,
        CategoryLinkManagementInterface $categoryLinkManagement,
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool
    ){
        $this->request = $request;
        $this->categoryLinkManagement = $categoryLinkManagement ?: ObjectManager::getInstance()
            ->get(CategoryLinkManagementInterface::class);
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * Observer execute
     *
     * @param Observer $observer
     * @return Observer
     * @throws CouldNotSaveException
     */
    public function execute(Observer $observer)
    {
        $moduleName     = $this->request->getModuleName();
        $controllerName = $this->request->getControllerName();
        $actionName     = $this->request->getActionName();
        if ($moduleName != 'marketplace' && $controllerName != 'index' && $actionName != 'productsaveedit') {
            return $this;
        }

        $product = $observer->getProduct();
        $newCategoryIds = (array)$this->request->getParam("category");

        $this->updateCategoryLinks($product, $newCategoryIds);

        return $this;
	}

    /**
     * Update category links
     *
     * @param Product $product
     * @param array $deleteLinks
     * @return array
     * @throws \Exception
     */
    protected function updateCategoryLinks($product, $newCategoryIds)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->getCategoryLinkMetadata()->getEntityTable();

        $query = $connection->select()
            ->from($tableName,['category_id'])
            ->where('product_id = ?', (int)$product->getId());

        $currentCategories = (array)$connection->fetchCol($query);

        $categoriesToRemove = array_diff($currentCategories, $newCategoryIds);
        $categoriesToAdd = array_diff($newCategoryIds, $currentCategories);

        if (count($categoriesToRemove)) {
            $connection->delete($tableName, [
                'product_id = ?' => (int)$product->getId(),
                'category_id IN(?)' => $categoriesToRemove
            ]);
        }

        foreach ($categoriesToAdd as $catId) {
            $connection->insert($tableName, [
                'product_id' => (int)$product->getId(),
                'category_id' => $catId
            ]);
        }

        return true;
    }

    /**
     * Get category link metadata
     *
     * @return EntityMetadataInterface
     * @throws \Exception
     */
    protected function getCategoryLinkMetadata()
    {
        if ($this->categoryLinkMetadata == null) {
            $this->categoryLinkMetadata = $this->metadataPool->getMetadata(CategoryLinkInterface::class);
        }

        return $this->categoryLinkMetadata;
    }
}
