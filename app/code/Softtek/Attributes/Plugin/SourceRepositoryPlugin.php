<?php
/**
 * Softtek Attributes Module
 *
 * @package Softtek_Attributes
 * @author Paul Soberanes <paul.soberanes@softtek.com>
 * @copyright Softtek 2020
 */
namespace Softtek\Attributes\Plugin;

use Magento\InventoryApi\Api\Data\SourceExtensionFactory;
use Magento\InventoryApi\Api\Data\SourceExtensionInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Inventory\Model\SourceRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Softtek\Attributes\Api\CustomSourceRepositoryInterface;

class SourceRepositoryPlugin
{
    /**
     * Source New Custom Attributes
     */
    const SELLER_ID           = 'seller_id';
    const SELLER_NAME         = 'seller_name';
    
    /**
     * @var SourceExtensionFactory
     */
    protected $extensionFactory;

    /**
     * SourceRepositoryPlugin constructor
     *
     * @param SourceExtensionFactory $extensionFactory
     */
    public function __construct(
        SourceExtensionFactory $extensionFactory
    ) {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Add "custom farmacias" extension attribute to source data to make accesible via API
     *
     * @param  SourceRepositoryInterface $subject
     * @param  SourceInterface $source
     * @return SourceInterface
     */
    public function afterGet(
        SourceRepositoryInterface $subject,
        SourceInterface $source
    ) {
        $extensionAttributes = $source->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();

        // Set all attributes
        $extensionAttributes->setSellerId($source->getData(self::SELLER_ID));
        $extensionAttributes->setSellerName($source->getData(self::SELLER_NAME));
       

        $source->setExtensionAttributes($extensionAttributes);

        return $source;
    }

    /**
     * Add "custom farmacias" extension attribute to source data to make accesible via API
     *
     * @param  SourceRepositoryInterface $subject
     * @param  SourceSearchResultsInterface $searchResult
     * @return SourceSearchResultsInterface
     */
    public function afterGetList(
        SourceRepositoryInterface $subject,
        SourceSearchResultsInterface $searchResult
    ) {
        $sources = $searchResult->getItems();

        foreach ($sources as &$source) {
            $extensionAttributes = $source->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();

            // Set all attributes
            $extensionAttributes->setSellerId($source->getData(self::SELLER_ID));
            $extensionAttributes->setSellerName($source->getData(self::SELLER_NAME));

            $source->setExtensionAttributes($extensionAttributes);
        }

        return $searchResult;
    }

    /**
     * Save "custom farmacias" extension attribute via API
     *
     * @param  SourceRepositoryInterface $subject
     * @param  string|null $sourceCode
     * @param  SourceInterface $source
     */
    public function afterSave(
        SourceRepositoryInterface $subject,
        $sourceCode = null,
        \Magento\InventoryApi\Api\Data\SourceInterface $source
    ) {
        $extensionAttributes = $source->getExtensionAttributes() ?: $this->extensionFactory->create();

        // Set all attributes
        $source->setSellerId($extensionAttributes->getSellerId());
        $source->setSellerName($extensionAttributes->getSellerName());
        $source->save();
    }

    public function beforeSave(
        SourceRepositoryInterface $subject,
        \Magento\InventoryApi\Api\Data\SourceInterface $source
    ) {
        $extensionAttributes = $source->getExtensionAttributes() ?: $this->extensionFactory->create();
        $dataSellerId = $extensionAttributes->getSellerId();
        $dataSellerName = $extensionAttributes->getSellerName();
    }
}
