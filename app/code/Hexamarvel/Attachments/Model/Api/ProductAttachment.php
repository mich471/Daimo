<?php
/**
 * @author Hexamarvel Team
 * @copyright Copyright (c) 2021 Hexamarvel (https://www.hexamarvel.com)
 * @package Hexamarvel_Attachments
 */
namespace Hexamarvel\Attachments\Model\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Filesystem\DirectoryList;
use Hexamarvel\Attachments\Api\ProductAttachmentInterface;
use Hexamarvel\Attachments\Model\AttachmentsFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Hexamarvel\Attachments\Model\ResourceModel\Attachments\CollectionFactory;
use Magento\Framework\Filesystem\Driver\File;

class ProductAttachment implements ProductAttachmentInterface
{
    /**
     * @var \Hexamarvel\Attachments\Model\Api\AttachmentsFactory
     */
    protected $attachment;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
      /**
       * @var \Magento\Framework\Webapi\Rest\Request
       */
    protected $request;
     /**
      * @var \Magento\Framework\Filesystem
      */
    protected $filesystem;
     /**
      * @var \Magento\Framework\Serialize\Serializer\Json
      */
    protected $helper;
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;
    /**
     * @var \Hexamarvel\Attachments\Model\ResourceModel\Attachments\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    protected $allowedFileTypes = [
        "pdf",
        "docx",
        "csv",
        "xlsx",
        "txt",
        "jpg",
        "jpeg",
        "png",
        "gif",
        "flv",
        "mpeg",
        "mp3",
        "mp4",
        "avi",
        "mov",
        "zip",
        "rar"
    ];

    /**
     * @param AttachmentsFactory $attachment
     * @param StoreManagerInterface $storeManager
     * @param Request $request
     * @param Filesystem $filesystem
     * @param Json $helper
     * @param SerializerInterface $serializer
     * @param CollectionFactory $collectionFactory
     * @param File $file
     */
    public function __construct(
        AttachmentsFactory $attachment,
        StoreManagerInterface $storeManager,
        Request $request,
        Filesystem $filesystem,
        Json $helper,
        SerializerInterface $serializer,
        CollectionFactory $collectionFactory,
        File $file
    ) {
        $this->attachment   = $attachment;
        $this->storeManager = $storeManager;
        $this->request      = $request;
        $this->filesystem   = $filesystem;
        $this->helper       = $helper;
        $this->serializer   = $serializer;
        $this->collectionFactory   = $collectionFactory;
        $this->file   = $file;
    }

    /**
     * @param int $attachmentId
     * @return obj attachment
     */
    public function get($id)
    {

        $attachment = $this->attachment->create()->load($id);
        if ($attachment->getId()) {
            if (!$attachment->getId()) {
                throw new NoSuchEntityException(
                    __('Attachment does not exist for the requested product.')
                );
            }

            $attachmentArray = $attachment->getData();
            $mediaPath = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            if ($attachmentArray['icon']) {
                $attachmentArray['icon'] = $mediaPath.'hexaattachment/products/icons/'.$attachmentArray['icon'];
            }

            $attachmentArray['file'] = $mediaPath.'hexaattachment/products/attachments/'.$attachmentArray['file'];
            if ($attachmentArray['products']) {
                $attachmentArray['products'] = array_keys($this->serializer->unserialize($attachmentArray['products']));
            }

            return [$attachmentArray];
        } else {
            throw new NoSuchEntityException(
                __("The attachment that was requested doesn't exist. Verify the attachment and try again.")
            );
        }
    }

    /**
     * @param int $attachmentId
     * @return obj attachment
     */
    public function getAttachmentsByProductId($id)
    {
        $attachments = $this->attachment->create()->getCollection()->addFieldtoFilter(
            'products',
            [
                'like' => '%"'.$id .'"%'
             ]
        );
        if (!count($attachments->getData())) {
            throw new NoSuchEntityException(
                __('Attachment does not exist for the requested product.')
            );
        }

        $mediaPath = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $attachments = $attachments->getData();
        foreach ($attachments as $key => $attachment) {
            if ($attachment['icon']) {
                $attachments[$key]['icon'] = $mediaPath.'hexaattachment/products/icons/'.$attachment['icon'];
            }

            $attachments[$key]['file'] = $mediaPath.'hexaattachment/products/attachments/'.$attachment['file'];
            if ($attachment['products']) {
                $attachments[$key]['products'] = array_keys($this->serializer->unserialize($attachment['products']));
            }
        }

        return [$attachments];
    }

    /**
     * @return obj $attachments
     */
    public function getList()
    {
        $mediaPath = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        /** @var Hexamarvel\Attachments\Model\ResourceModel\Attachments\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->load();

        $attachments = $collection->getData();
        foreach ($attachments as $key => $attachment) {
            if ($attachment['icon']) {
                $attachments[$key]['icon'] = $mediaPath.'hexaattachment/products/icons/'.$attachment['icon'];
            }

            $attachments[$key]['file'] = $mediaPath.'hexaattachment/products/attachments/'.$attachment['file'];
            if ($attachment['products']) {
                $attachments[$key]['products'] = array_keys($this->serializer->unserialize($attachment['products']));
            }
        }

        return $attachments;
    }

    /**
     * @param int $attachmentId
     * @return bool true on success
     */
    public function deleteById($id)
    {
        $attachment = $this->attachment->create()->load($id);
        if ($attachment->getId()) {
            $attachment->delete();
        } else {
            throw new \Magento\Framework\Exception\StateException(
                __("The attachment couldn't be removed.")
            );
        }

        return true;
    }

    /**
     * @return bool true on success
     */
    public function save()
    {
        $body = $this->request->getBodyParams();
        if (!isset($body['attachment'])) {
            throw new \Magento\Framework\Exception\StateException(
                __('Please check param to create attachment.')
            );
        }
        $attach = $body['attachment'];
        $mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $iconPath = $mediaPath . 'hexaattachment/products/icons/';
        $filePath = $mediaPath . 'hexaattachment/products/attachments/';
        if (isset($attach['icon']) && !empty($attach['icon'])) {
            $iconName = str_replace(" ", "_", $attach['icon']['name']);
            $base64String = $attach['icon']['base64_encoded_data'];
            $mime_type = explode('/', mime_content_type($base64String))[1];
            if ($mime_type == "jpg" || $mime_type == "png" || $mime_type =="jpeg" || $mime_type =="gif") {
                $data = explode(',', $base64String);
                $content = base64_decode($data[1]);
                $file = $this->file->fileOpen($iconPath . $iconName, "wb");
                $this->file->fileWrite($file, $content);
                $this->file->fileClose($file);
            } else {
                throw new \Magento\Framework\Exception\StateException(
                    __('Icon Image type not supported')
                );
            }
            $attach['icon']['name'] = $iconName;
        }

        if (isset($attach['file']) && !empty($attach['file'])) {
            $fileName = str_replace(" ", "_", $attach['file']['name']);
            $base64String = $attach['file']['base64_encoded_data'];
            $mime_type = explode('/', mime_content_type($base64String))[1];
            if (in_array($mime_type, $allowedFileTypes)) {
                $data = explode(',', $base64String);
                $content = base64_decode($data[1]);
                $file = $this->file->fileOpen($filePath . $fileName, "wb");
                $this->file->fileWrite($file, $content);
                $this->file->fileClose($file);
            } else {
                throw new \Magento\Framework\Exception\StateException(
                    __('File type not supported')
                );
            }
        } else {
            throw new \Magento\Framework\Exception\StateException(
                __('Attachment file is required')
            );
        }

        if (!isset($attach['name']) || empty($attach['name'])) {
            throw new \Magento\Framework\Exception\StateException(
                __('Attachment name is required.')
            );
        }
        if (!isset($attach['customer_group']) || empty($attach['customer_group'])) {
            throw new \Magento\Framework\Exception\StateException(
                __('Customer group is required.')
            );
        }

        if (!isset($attach['stores']) || empty($attach['stores'])) {
            throw new \Magento\Framework\Exception\StateException(
                __('Store Id is required.')
            );
        }

        $productData = [];
        foreach ($attach['products'] as $product) {
            $productData[$product] = "";
        }

        if (isset($attach['icon'])) {
            $attach['icon'] = $attach['icon']['name'];
        }
        $attach['file'] = $attach['file']['name'];
        $attach['products'] = $this->helper->serialize($productData);
        $attach['customer_group'] = implode(",", $attach['customer_group']);
        $attach['stores'] = implode(",", $attach['stores']);
        $attachment = $this->attachment->create()->setData($attach);
        $attachment->save();

        return true;
    }

    /**
     * @param int $attachmentId
     * @return bool true on success
     */
    public function update($id)
    {
        $attachment = $this->attachment->create()->load($id);
        if ($attachment->getId()) {
            $body = $this->request->getBodyParams();
            if (!isset($body['attachment'])) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Please check param to create attachment.')
                );
            }
            $attach = $body['attachment'];
            $mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
            $iconPath = $mediaPath . 'hexaattachment/products/icons/';
            $filePath = $mediaPath . 'hexaattachment/products/attachments/';
            $updatedData = [];
            if (isset($attach['icon']) && !empty($attach['icon'])) {
                $iconName = str_replace(" ", "_", $attach['icon']['name']);
                $base64String = $attach['icon']['base64_encoded_data'];
                $mime_type = explode('/', mime_content_type($base64String))[1];
                if ($mime_type == "jpg" || $mime_type == "png" || $mime_type =="jpeg" || $mime_type =="gif") {
                    $data = explode(',', $base64String);
                    $content = base64_decode($data[1]);
                    $file = $this->file->fileOpen($iconPath . $iconName, "wb");
                    $this->file->fileWrite($file, $content);
                    $this->file->fileClose($file);
                } else {
                    throw new \Magento\Framework\Exception\StateException(
                        __('Icon Image type not supported')
                    );
                }
                $attach['icon'] = $iconName;
            }

            if (isset($attach['file']) && !empty($attach['file'])) {
                $fileName = str_replace(" ", "_", $attach['file']['name']);
                $base64String = $attach['file']['base64_encoded_data'];
                $mime_type = explode('/', mime_content_type($base64String))[1];
                if ($mime_type == "pdf" || $mime_type == "doc" || $mime_type =="txt") {
                    $data = explode(',', $base64String);
                    $content = base64_decode($data[1]);
                    $file = $this->file->fileOpen($filePath . $fileName, "wb");
                    $this->file->fileWrite($file, $content);
                    $this->file->fileClose($file);
                } else {
                    throw new \Magento\Framework\Exception\StateException(
                        __('File type not supported')
                    );
                }
                $attach['file'] = $attach['file']['name'];
            }

            if (isset($attach['products'])) {
                $productData = [];
                foreach ($attach['products'] as $product) {
                    $productData[$product] = "";
                }

                $attach['products'] = $this->helper->serialize($productData);
            }
            if (isset($attach['customer_group'])) {
                $attach['customer_group'] = implode(",", $attach['customer_group']);
            }
            if (isset($attach['stores'])) {
                $attach['stores'] = implode(",", $attach['stores']);
            }

            $attach['id'] = $id;
            $attachment->setData($attach)->save();

            return true;
        } else {
            throw new NoSuchEntityException(
                __("The attachment couldn't be removed.")
            );
        }
    }
}
