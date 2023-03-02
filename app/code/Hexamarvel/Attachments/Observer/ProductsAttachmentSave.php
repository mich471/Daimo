<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductsAttachmentSave implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Hexamarvel\Attachments\Model\AttachmentsFactory
     */
    protected $attachmentFactory;

    /**
     * Constructor
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Hexamarvel\Attachments\Model\AttachmentsFactory $attachmentFactory
    ) {
        $this->request = $request;
        $this->attachmentFactory = $attachmentFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getDataObject();
        $postData = $this->request->getPost();
        $post = $postData['product'];
        if (isset($post['attach'])) {
            $attachmentId = json_decode($post['attach'], true);
            if (!empty($attachmentId)) {
                foreach ($attachmentId as $key => $value) {
                    $rowData = $this->attachmentFactory->create()->load($key);
                    $productIds = [];
                    $productIds = json_decode($rowData->getProducts(), true);
                    if (!array_key_exists($product->getId(), $productIds)) {
                        $productIds[$product->getId()] = '';
                    }

                    $rowData->setProducts(json_encode($productIds));
                    $rowData->save();
                }
            }

            $attachments = $this->attachmentFactory->create()->getCollection()->addFieldtoFilter(
                'products',
                [
                    'like' => '%"'. $product->getId() .'"%'
                 ]
            );

            foreach ($attachments as $key => $rowData) {
                $productIds = [];
                if (!array_key_exists($rowData->getId(), $attachmentId)) {
                    $productIds = json_decode($rowData->getProducts(), true);
                    unset($productIds[$product->getId()]);
                    $rowData->setProducts(json_encode($productIds));
                    $rowData->save();
                }
            }
        }
    }
}
