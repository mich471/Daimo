<?php
namespace Softtek\Marketplace\Observer\Customer\Account;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\CustomerFactory as ResourceCustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Eav\Model\AttributeSetManagement;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Purpletree\Marketplace\Helper\Data;
use Purpletree\Marketplace\Model\ResourceModel\Seller as ResourceSeller;
use Purpletree\Marketplace\Model\Seller;
use Purpletree\Marketplace\Model\SellerFactory;
use Softtek\Marketplace\Helper\Data as SofttekData;
use Softtek\Marketplace\Observer\Customer\Account\CouldNotSaveException;
use Softtek\Marketplace\Observer\Customer\Account\Observer;
use Softtek\Marketplace\Plugin\Customer\Account\Create;
use function Softtek\Marketplace\Plugin\Customer\Account\sizeof;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Purpletree\Marketplace\Model\Upload;

class CreatePostAfter implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @var Data
     */
    protected $stmHelper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Upload
     */
    protected $uploadModel;

    /**
     * Observer constructor.
     *
     * @param UrlInterface $urlInterface
     * @param SofttekData $stmHelper
     * @param CustomerSession $customer
     * @param Seller $store
     * @param CustomerFactory $customerFactory
     * @param ResourceCustomerFactory $customerfactorysave
     * @param ResourceSeller $storeDetails
     * @param SellerFactory $sellerFactory
     * @param AttributeSetManagement $attributeSetManagement
     * @param TypeFactory $eavTypeFactory
     * @param Data $dataHelper
     * @param AttributeSetFactory $attributeSetFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param IndexerRegistry $indexerRegistry
     * @param Upload $uploadModel
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        UrlInterface $urlInterface,
        SofttekData $stmHelper,
        CustomerSession $customer,
        Seller $store,
        CustomerFactory $customerFactory,
        ResourceCustomerFactory $customerfactorysave,
        ResourceSeller $storeDetails,
        SellerFactory $sellerFactory,
        AttributeSetManagement $attributeSetManagement,
        TypeFactory $eavTypeFactory,
        Data $dataHelper,
        AttributeSetFactory $attributeSetFactory,
        ScopeConfigInterface $scopeConfig,
        IndexerRegistry $indexerRegistry,
        ManagerInterface $messageManager,
        Upload $uploadModel
    )
    {
        $this->urlInterface = $urlInterface;
        $this->stmHelper = $stmHelper;
        $this->indexerRegistry = $indexerRegistry;
        $this->customer = $customer;
        $this->attributeSetManagement = $attributeSetManagement;
        $this->customerFactory = $customerFactory;
        $this->dataHelper = $dataHelper;
        $this->storeDetails = $storeDetails;
        $this->customerfactorysave = $customerfactorysave;
        $this->sellerFactory = $sellerFactory;
        $this->store = $store;
        $this->_eavTypeFactory = $eavTypeFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->uploadModel = $uploadModel;
    }

    /**
     * Observer execute
     *
     * @param Observer $observer
     * @return Observer
     * @throws CouldNotSaveException
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->stmHelper->isEnabled()) {
            return $this;
        }

        $accountController = $observer->getAccountController();
        $newCustomer = $observer->getCustomer();
        $customerId = $newCustomer->getId();
        $moduleEnable = $this->dataHelper->getGeneralConfig('general/enabled');
        $sellerReq = $this->dataHelper->getGeneralConfig('general/seller_approval_required');
        $data = $accountController->getRequest()->getPostValue();
        $is_seller = $sellerReq == 0 ? 1 : 2;
        if (!$moduleEnable) {
            return $this;
        }
        if ($data) {
            try {
                if (!isset($data['ut'])) {
                    return $this;
                }

                $this->store->setSellerId($customerId);
                $this->store->setStatusId($is_seller);
                $this->saveattributeValue(1, $customerId);
                $this->store->save();

                $this->messageManager->addSuccess(__('Waiting for admin approval'));
                $data['successful_creation'] = true;
                $this->customer->setSellerFormData($data);

                $message = '';
                $messagecatch = '';
                if ($sellerReq) {
                    $message = 'Novo vendedor registrado.';
                } else {
                    $message = 'New registered seller. Awaiting pre-registration approval.';
                }
                try {
                    $this->mailToAdmin($message, $newCustomer);
                    //$this->mailToSeller($message, $newCustomer);
                } catch (LocalizedException $e) {
                    $messagecatch = $e->getMessage();
                } catch (\RuntimeException $e) {
                    $messagecatch = $e->getMessage();
                } catch (\Exception $e) {
                    $messagecatch = 'Something went wrong.';
                }
                return $this;
            } catch (\Exception $e) {
                throw new LocalizedException(__('Something went wrong while saving the details'));
            }
        }

        return $this;
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
     * Refresh customer entity
     *
     * @return NULL
     */
    public function reIndexCustomer($customerId)
    {
        $indexerIds = ['customer_grid'];
        $startTime = microtime(true);
        foreach ($indexerIds as $indexerId) {
            try {
                $indexer = $this->indexerRegistry->get($indexerId);
                $indexer->reindexAll($customerId);
            } catch (LocalizedException $e) {
                throw new LocalizedException($indexer->getTitle() . ' indexer process unknown error:', $e->getMessage());
            } catch (\Exception $e) {
                throw new LocalizedException(__("We couldn't reindex data because of an error."));
            }
        }
    }

    /**
     * Mail to seller
     *
     * @return void
     */
    public function mailToSeller($message, $customer)
    {
        $customerFirstName = $customer->getFirstName();
        $customerLastName = $customer->getLastName();
        $customerEmail = $customer->getEmail();
        $identifier    = $this->scopeConfig->getValue( 'purpletree_marketplace/general/seller_registration_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        try {
            $emailTemplateVariables = [];
            $emailTemplateVariables['name'] = $customerFirstName;
            $emailTemplateVariables['sellername'] = $customerFirstName.' '.$customerLastName;
            $emailTemplateVariables['selleremail'] = $customerEmail;
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
            throw new LocalizedException(__('Not Able to send Email'));
        }
    }

    /**
     * Mail to admin
     *
     * @return void
     */
    public function mailToAdmin($message, $customer)
    {
        $customerFirstName = $customer->getFirstName();
        $customerLastName = $customer->getLastName();
        $customerEmail = $customer->getEmail();
        $identifier    = $this->scopeConfig->getValue( 'purpletree_marketplace/general/seller_registration_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        try {
            $emailTemplateVariables = [];
            $emailTemplateVariables['name'] = $this->getStoreName();
            $emailTemplateVariables['sellername'] = $customerFirstName.' '.$customerLastName;
            $emailTemplateVariables['selleremail'] = $customerEmail;
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
            throw new LocalizedException(__('Not Able to send Email'));
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

    /**
     * Admin Store Name
     *
     * @return  Admin Store Email
     */
    public function getStoreName()
    {
        return $this->scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
