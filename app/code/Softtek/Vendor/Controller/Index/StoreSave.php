<?php

namespace Softtek\Vendor\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerFactorySave;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Purpletree\Marketplace\Model\SellerFactory;
use Psr\Log\LoggerInterface;

class StoreSave extends Action
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var SellerFactory
     */
    protected $sellerFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Purpletree\Marketplace\Model\ResourceModel\Seller
     */
    protected $storeDetails;
    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;
    /**
     * @var CollectionFactory
     */
    protected $customerCollectionFactory;
    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerInterfaceFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var CustomerFactorySave
     */
    protected $customerFactorySave;
    /**
     * @var EncryptorInterface
     */
    protected $encryptorInterface;
    /**
     * @var AddressFactory
     */
    protected $addressFactory;
    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param \Magento\MediaStorage\Model\File\UploaderFactory
     * @param \Purpletree\Marketplace\Model\Upload
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Purpletree\Marketplace\Model\SellerFactory
     * @param \Magento\Framework\Registry
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Framework\App\Action\Context
     *
     */
    public function __construct(
        \Purpletree\Marketplace\Model\Upload $uploadModel,
        CustomerSession $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        AccountManagementInterface $customerAccountManagement,
        CollectionFactory $customerCollectionFactory,
        CustomerInterfaceFactory $customerInterfaceFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CustomerFactory $customerFactory,
        CustomerFactorySave $customerFactorySave,
        EncryptorInterface $encryptorInterface,
        AddressFactory $addressFactory,
        AccountRedirect $accountRedirect,
        LoggerInterface $logger,
        Context $context
    ) {
        $this->customerSession            =       $customerSession;
        $this->sellerFactory        =       $sellerFactory;
        $this->uploadModel          =       $uploadModel;
        $this->storeManager         =       $storeManager;
        $this->coreRegistry         =       $coreRegistry;
        $this->storeDetails             =       $storeDetails;
        $this->resultForwardFactory =       $resultForwardFactory;
        $this->dataHelper           =       $dataHelper;
        $this->logger = $logger;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerFactory = $customerFactory;
        $this->customerFactorySave = $customerFactorySave;
        $this->encryptorInterface = $encryptorInterface;
        $this->addressFactory = $addressFactory;
        $this->accountRedirect = $accountRedirect;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/account/index');

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try{
                $storeId = $this->storeManager->getStore()->getId();
                $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
                $email = $data['email'];
                $firstName = $data['firstname'];
                $lastName = $data['lastname'];
                $password = $data['password'];
                $passwordConfirmation = $data['password_confirmation'];
                $storeName = $data['store_name'];
                $storeUrl = $data['store_url'];
                $typeCompany = $data['type_company'];
                $operatingSegment = $data['operating_segment'];
                $cnpj = $data['cnpj'];
                $socialname = $data['socialname'];

                $countryId = $data['country_id'];
                $regionId = $data['region_id'];
                $city = $data['city'];
                $postcode = $data['postcode'];
                $telephone = $data['telephone'];

                $isCustomer = true;

                $customer = $this->customerCollectionFactory->create();
                $customer->addFilter('website_id', $websiteId);
                $customer->addFilter('email', $email);

                if (!$customer->getData()){
                    $isCustomer = false;
                    $customer = $this->customerInterfaceFactory->create();
                    $customer->setWebsiteId($websiteId);
                    $customer->setEmail($email);
                    $customer->setFirstname($firstName);
                    $customer->setLastname($lastName);
                    $customer->setCustomAttribute('type_company', $typeCompany);
                    $customer->setCustomAttribute('operating_segment', $operatingSegment);
                    $customer->setCustomAttribute('cnpj', $cnpj);
                    $customer->setCustomAttribute('socialname', $socialname);
                    $this->checkPasswordConfirmation($password, $passwordConfirmation);
                    $hashedPassword = $this->encryptorInterface->getHash($password, true);
                    $this->customerRepositoryInterface->save($customer, $hashedPassword);

                    $customer = $this->customerCollectionFactory->create();
                    $customer->addFilter('website_id', $websiteId);
                    $customer->addFilter('email', $email);
                }

                $customer = $customer->getData();
                $customer = end($customer);
                $customerId = $customer['entity_id'];

                if(!$isCustomer){
                    $address = $this->addressFactory->create();
                    $address->setCustomerId($customerId)
                        ->setFirstname($firstName)
                        ->setLastname($lastName)
                        ->setCountryId($countryId)
                        ->setRegionId($regionId)
                        ->setCity($city)
                        ->setPostcode($postcode)
                        ->setStreet($city)
                        ->setTelephone($telephone)
                        ->setIsDefaultBilling('1')
                        ->setIsDefaultShipping('1')
                        ->setSaveInAddressBook('1');
                    $address->save();
                }

                $customerData = $this->customerRepositoryInterface->getById($customerId);
                $isSeller = $customerData->getCustomAttribute('is_seller')->getValue();

                if (!$isSeller) {
                    $sellerdata = $this->sellerFactory->create();
                    $sellerdata->setStoreName($storeName);
                    $sellerdata->setStoreUrl($storeUrl);
                    $sellerdata->setSellerId($customerId);
                    $sellerdata->save();
                    $customerData->setCustomAttribute('is_seller', 1);
                    $this->customerRepositoryInterface->save($customerData);
                }else{
                    $this->messageManager->addErrorMessage(__('You are already a registered seller.'));
                    return $resultRedirect;
                }

                if(!$isCustomer && !$isSeller){
                    $customer = $this->customerFactory->create();
                    $loadCustomer = $customer->setWebsiteId($websiteId)->loadByEmail($email);
                    $this->customerSession->setCustomerAsLoggedIn($loadCustomer);
                    $this->messageManager->addSuccessMessage(__('Successful seller registration.'));
                }

                return $resultRedirect;

            }catch (InputException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addErrorMessage($error->getMessage());
                }
            }
            catch (LocalizedException $e){
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect;

    }

    /**
     *
     * @return bool
     */
    public function emailExistOrNot($email)
    {
        $websiteId = $this->storeManager->getWebsite()->getId();
        return $this->customerAccountManagement->isEmailAvailable($email, $websiteId);
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password
     * @param string $confirmation
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }
}
