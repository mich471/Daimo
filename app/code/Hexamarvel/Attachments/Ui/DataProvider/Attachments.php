<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Ui\DataProvider;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Hexamarvel\Attachments\Model\ResourceModel\Attachments\CollectionFactory;

class Attachments extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $attachmentFactory
     * @param StoreManagerInterface $storeManager
     * @param DirectoryList $directoryList
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $attachmentFactory,
        StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $attachmentFactory->create();
        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $items = $this->collection->getItems();
        $data = [];
        foreach ($items as $attachment) {
            $_data = $attachment->getData();
            if (isset($_data['icon'])) {
                $iconUrl = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'hexaattachment/products/icons/' . $attachment->getIcon();

                $iconSize = filesize(
                    $this->directoryList->getPath('media').
                    '/hexaattachment/products/icons/' .
                    $attachment->getIcon()
                );
                $_data['icon'] = [
                    [
                        'name' => $attachment->getIcon(),
                        'url' => $iconUrl,
                        'size' => (int)$iconSize
                    ]
                ];
            }

            if (isset($_data['file'])) {
                $fileUrl = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'hexaattachment/products/attachments/' . $attachment->getFile();

                $fileSize = filesize(
                    $this->directoryList->getPath('media') .
                    '/hexaattachment/products/attachments/' .
                    $attachment->getFile()
                );
                $_data['file'] = [
                    [
                        'name' => $attachment->getFile(),
                        'url' => $fileUrl,
                        'size' => (int)$fileSize
                    ]
                ];
            }

            $attachment->setData($_data);
            $data[$attachment->getId()] = $_data;
        }

        if (!empty($data)) {
            return $data;
        }
    }
}
