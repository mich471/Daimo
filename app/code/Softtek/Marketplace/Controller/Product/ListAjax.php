<?php
namespace Softtek\Marketplace\Controller\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\Controller\ResultFactory;
use PHPCuong\ProductQuestionAndAnswer\Controller\Product\ListAjax as ProductListAjax;

class ListAjax extends ProductListAjax
{
    /**
     * Show list of product's questions
     *
     * @return \Magento\Framework\Controller\Result\RawFactory|\Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $credentials = null;
        $httpBadRequestCode = 400;

        /** @var \Magento\Framework\Controller\Result\RawFactory $resultRawFactory */
        $resultRaw = $this->resultRawFactory->create();
        try {
            $credentials = $this->getRequest()->getParams();
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        if (!$credentials || $this->getRequest()->getMethod() !== 'GET' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $productId = (int) $credentials['id'];
        $pageId = (!empty($credentials['page']) && (int) $credentials['page'] > 0) ? (int) $credentials['page'] : 1;
        $pageSize = $this->questionData->getPageSize();
        if ($productId > 0) {
            try {
                $product = $this->productRepository->getById($productId, false, $this->storeManager->getStore()->getId());
                $collection = $this->questionCollectionFactory->create()->addStoreFilter(
                    ['0', $this->storeManager->getStore()->getId()]
                )->addFieldToFilter(
                    'main_table.entity_pk_value', $productId
                )->addStatusFilter(
                    \PHPCuong\ProductQuestionAndAnswer\Model\Status::STATUS_APPROVED
                )->addVisibilityFilter(
                    \PHPCuong\ProductQuestionAndAnswer\Model\Visibility::VISIBILITY_VISIBLE
                )->addProductIdFilter(
                    $productId
                )->setPageSize(
                    $pageSize
                )->setCurPage(
                    $pageId
                )->setDateOrder();
                $answersTableName = $collection->getConnection()->getTableName("phpcuong_product_answer");
                $collection->join(['answers' => $answersTableName], 'main_table.question_id = answers.question_id', 'answer_detail');

                $this->coreRegistry->register('phpcuong_question_product', $collection);
            } catch (NoSuchEntityException $e) {}
        }

        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        return $resultLayout;
    }
}
