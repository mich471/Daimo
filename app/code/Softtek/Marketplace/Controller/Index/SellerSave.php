<?php
/**
 * Purpletree_Marketplace SellerSave
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Softtek\Marketplace\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use \Magento\Customer\Model\Session as CustomerSession;
use Purpletree\Marketplace\Model\Upload;
use Purpletree\Marketplace\Model\Seller;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\CustomerFactory as ResourceCustomerFactory;
use Purpletree\Marketplace\Model\ResourceModel\Seller as ResourceSeller;
use Magento\Framework\Controller\Result\ForwardFactory;
use Purpletree\Marketplace\Model\SellerFactory;
use Magento\Eav\Model\AttributeSetManagement;
use Magento\Eav\Model\Entity\TypeFactory;
use Purpletree\Marketplace\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Indexer\IndexerRegistry;

class SellerSave extends Action
{

    /**
     * Constructor
     *
     * @param Session $customer
     * @param Seller $store
     * @param CustomerFactory $customerFactory
     * @param ResourceCustomerFactory $customerfactorysave
     * @param ResourceSeller $storeDetails
     * @param ForwardFactory $resultForwardFactory
     * @param SellerFactory $sellerFactory
     * @param AttributeSetManagement $attributeSetManagement
     * @param TypeFactory $eavTypeFactory
     * @param Data $dataHelper
     * @param AttributeSetFactory $attributeSetFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param IndexerRegistry $indexerRegistry
     * @param Upload $uploadModel
     * @param Context $context
     */
    public function __construct(
        CustomerSession $customer,
        Seller $store,
        CustomerFactory $customerFactory,
        ResourceCustomerFactory $customerfactorysave,
        ResourceSeller $storeDetails,
        ForwardFactory $resultForwardFactory,
        SellerFactory $sellerFactory,
        AttributeSetManagement $attributeSetManagement,
        TypeFactory $eavTypeFactory,
        Data $dataHelper,
        AttributeSetFactory $attributeSetFactory,
        ScopeConfigInterface $scopeConfig,
        IndexerRegistry $indexerRegistry,
        Upload $uploadModel,
        Context $context
    ) {
        $this->indexerRegistry       =       $indexerRegistry;
        $this->customer              =      $customer;
        $this->attributeSetManagement          =      $attributeSetManagement;
        $this->customerFactory       =      $customerFactory;
        $this->dataHelper            =      $dataHelper;
        $this->storeDetails          =      $storeDetails;
        $this->customerfactorysave   =      $customerfactorysave;
        $this->resultForwardFactory  =      $resultForwardFactory;
        $this->sellerFactory         =      $sellerFactory;
        $this->store                 =      $store;
        $this->_eavTypeFactory       =      $eavTypeFactory;
        $this->attributeSetFactory   =      $attributeSetFactory;
        $this->scopeConfig           =      $scopeConfig;
        $this->uploadModel = $uploadModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        $sellerReq=$this->dataHelper->getGeneralConfig('general/seller_approval_required');
        $data = $this->getRequest()->getPostValue();
        if (!$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        if ($data) {
            $this->customer->setSellerFormData($data);
            if (!isset($data['entity_idpts'])) {
                $this->messageManager->addError(__('Invalid Store ID.'));
                return $this->_redirect('marketplace/index/becomeseller');
            }
            $this->store = $this->store->load($data['entity_idpts']);
            if (isset($data['store_url'])) {
                    $samurl = sizeof($this->storeDetails->checkUniqueUrl($data['store_url']));
                if ($samurl != 0) {
                    $this->messageManager->addError(__('Store Url already exits. Choose different store url.'));
                    return $this->_redirect('marketplace/index/becomeseller');
                }
            }
            try {
                if ($data['again_seller'] == 0) {
                    $this->store->setSellerId($customerId);
                    $this->store->setStoreUrl($data['store_url']);
                    $this->saveattributeValue(1, $customerId);
                    //------------------------------------------------
                    $banner = $this->uploadModel->uploadFileAndGetName('banner', $data, $data['store_old_banner']);
                    $logo = $this->uploadModel->uploadFileAndGetName('logo', $data, $data['store_old_logo']);
                    $this->store->setStoreName($data['store_name']);

                    preg_match_all('!\d+!', $data['store_phone'], $pnMatches);
                    $phoneNumber = implode("", $pnMatches[0]);
                    $this->store->setStorePhone($phoneNumber);

                    if ($banner!='') {
                        $this->store->setStoreBanner('marketplace'.$banner);
                    } else {
                        $this->store->setStoreBanner($data['store_old_banner']);
                    }
                    if ($logo!='') {
                        $this->store->setStoreLogo('marketplace'.$logo);
                    } else {
                        $this->store->setStoreLogo($data['store_old_logo']);
                    }
                    $this->store->setStoreDescription($data['store_description']);
                    $this->store->setStoreShippingPolicy($data['store_shipping_policy']);
                    $this->store->setStoreReturnPolicy($data['store_return_policy']);
                    $this->store->setStoreMetaKeywords($data['store_meta_keywords']);
                    $this->store->setStoreMetaDescriptions($data['store_meta_descriptions']);
                    $this->store->setStoreAddress($data['store_address']);
                    $this->store->setStoreCity($data['store_city']);
                    if (is_numeric($data['store_region'])) {
                        $this->store->setStoreRegionId($data['store_region']);
                        $this->store->setStoreRegion('');
                    } else {
                        $this->store->setStoreRegion($data['store_region']);
                        $this->store->setStoreRegionId(0);
                    }
                    $this->store->setStoreCountry($data['store_country']);
                    $this->store->setStoreZipcode($data['store_zipcode']);
                    $this->store->setStoreTinNumber($data['store_tin_number']);
                    $this->store->setStoreBankAccount($data['store_bank_account']);
                    //------------------------------------------------
                    if ($data["status_id"] == 3) {
                        $this->store->setStatusId(4);
                    }
                    $this->store->save();
                    $entityTypeCode = 'catalog_product';
                    $entityType     = $this->_eavTypeFactory->create()->loadByCode($entityTypeCode);
                    $defaultSetId   = $entityType->getDefaultAttributeSetId();
                    $datas = [
                        'attribute_set_name'    => "Default_seller_".$customerId,
                        'entity_type_id'        => $entityType->getId()
                    ];
                    $attributeSet = $this->attributeSetFactory->create();
                    $attributeSet->setData($datas);
                    $this->attributeSetManagement->create($entityTypeCode, $attributeSet, $defaultSetId);
                    $this->messageManager->addSuccessMessage(__('Updated seller information'));
                    $this->customer->unsSellerFormData();
                }

                $url = 'marketplace/index/becomeseller';
                if ($data["status_id"] == 3) {
                    $this->messageManager->addSuccessMessage(__('Waiting for admin data approval'));
                    $message='';
                    if ($sellerReq) {
                        $message='Novo vendedor registrado.';
                    } else {
                        $message = __('New registered seller. Pending to approve store data.');
                    }
                    try {
                        $cuystomer = $this->customer->getCustomer();
                        $this->mailToAdmin($message, $data['store_url'], $cuystomer);
                        //$this->mailToSeller($message, $data['store_url'], $cuystomer);
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $messagecatch = $e->getMessage();
                    } catch (\RuntimeException $e) {
                        $messagecatch = $e->getMessage();
                    } catch (\Exception $e) {
                        $messagecatch = 'Something went wrong.';
                    }
                    $url = 'sellerinfo/index/registrationstep4';
                }

                return $this->_redirect($url);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the details'));
            }
        }
        return $this->_redirect('marketplace/index/becomeseller');
    }
    public function reIndexCustomer($customerId)
    {
        $indexerIds = ['customer_grid'];
        $startTime = microtime(true);
        foreach ($indexerIds as $indexerId) {
            try {
                $indexer = $this->indexerRegistry->get($indexerId);
                $indexer->reindexAll($customerId);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($indexer->getTitle() . ' indexer process unknown error:', $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __("We couldn't reindex data because of an error.".$e->getMessage()));
            }
        }
    }
    /**
     * Save Customer Attribute Value
     *
     * @return NULL
     */
    public function saveattributeValue($value, $customerId)
    {
        $customer = $this->customerFactory->create();
        $customerData = $customer->getDataModel();
        $customerData->setId($customerId);
        $customerData->setCustomAttribute('is_seller', $value);
        $customer->updateData($customerData);
        $customerResource = $this->customerfactorysave->create();
        $customerResource->saveAttribute($customer, 'is_seller');
         $this->reIndexCustomer($customerId);
    }

    /**
     *   Registration Email
     *
     *
     */
    public function mailToSeller($message, $store_url, $customer)
    {
        $customerFirstName = $customer->getFirstName();
        $customerLastName = $customer->getLastName();
        $customerEmail = $customer->getEmail();
        $identifier    = 'seller_ask_final_approval_email';
        try {
            $emailTemplateVariables = [];
             $emailTemplateVariables['name'] = $customerFirstName;
            $emailTemplateVariables['sellername'] = $customerFirstName.' '.$customerLastName;
            $emailTemplateVariables['selleremail'] = $customerEmail;
            $emailTemplateVariables['store_url'] = $store_url;
            $emailTemplateVariables['message'] = $message;
            $error = false;
            $sender = [
            'name' => $this->getStoreName(),
            'email' =>$this->getStoreEmail()
            ];
            $receiver = [
            'name' => $customerFirstName,
            'email' =>$customerEmail
            ];
            $this->dataHelper->yourCustomMailSendMethod(
                $emailTemplateVariables,
                $sender,
                $receiver,
                $identifier
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Not Able to send Email'.$e->getMessage()));
        }
    }
    public function mailToAdmin($message, $store_url, $customer)
    {
         $customerFirstName = $customer->getFirstName();
         $customerLastName = $customer->getLastName();
         $customerEmail = $customer->getEmail();
        $identifier    = $this->scopeConfig->getValue( 'purpletree_marketplace/general/seller_ask_final_approval_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        try {
            $emailTemplateVariables = [];
             $emailTemplateVariables['name'] = $this->getStoreName();
            $emailTemplateVariables['sellername'] = $customerFirstName.' '.$customerLastName;
            $emailTemplateVariables['selleremail'] = $customerEmail;
            $emailTemplateVariables['store_url'] = $store_url;
            $emailTemplateVariables['message'] = $message;
            $error = false;
            $sender = [
            'name' => $this->getStoreName(),
            'email' =>$this->getStoreEmail()
            ];
            $receiver = [
            'name' => $this->getStoreName(),
            'email' =>$this->getStoreEmail()
            ];
            $this->dataHelper->yourCustomMailSendMethod(
                $emailTemplateVariables,
                $sender,
                $receiver,
                $identifier
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Not Able to send Email'.$e->getMessage()));
        }
    }

    /**
     * Admin Store Email
     *
     * @return  Admin Store Email
     */
    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue('sportico/general/notify_reverse_transactions_mail', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getStoreName()
    {
        return $this->scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
