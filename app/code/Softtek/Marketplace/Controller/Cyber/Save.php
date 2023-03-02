<?php
/**
 * Softtek_Marketplace SellerData
 *
 * @category    Softtek
 * @package     Softtek_Marketplace
 * @author      J. Abraham Serena <jorge.serena@softtek.com>
 * @copyright   © Softtek 2022. All rights reserved.
 */
namespace Softtek\Marketplace\Controller\Cyber;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Message\ManagerInterface;
use Purpletree\Marketplace\Helper\Data;
use Purpletree\Marketplace\Model\ResourceModel\Seller as ResourceSeller;
use Purpletree\Marketplace\Model\Seller;
use Magento\Framework\Encryption\EncryptorInterface;

class Save extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var CustomerSession
     */
    protected $customer;

    /**
     * @var ResourceSeller
     */
    protected $storeDetails;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Seller
     */
    protected $store;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * Save Constructor
     *
     * @param Context $context
     * @param CustomerSession $customer
     * @param ResourceSeller $storeDetails
     * @param Data $dataHelper
     * @param Seller $store
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        CustomerSession $customer,
        ResourceSeller $storeDetails,
        Data $dataHelper,
        Seller $store,
        EncryptorInterface $encryptor
    )
    {
        $this->customer             =      $customer;
        $this->storeDetails         =      $storeDetails;
        $this->dataHelper           =      $dataHelper;
        $this->store                =      $store;
        $this->encryptor            =      $encryptor;

        return parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(\Magento\Framework\App\RequestInterface $request): ?\Magento\Framework\App\Request\InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerId = $this->customer->getCustomer()->getId();
        $seller = $this->storeDetails->storeId($customerId);
        $moduleEnable = $this->dataHelper->getGeneralConfig('general/enabled');
        $data = $this->getRequest()->getPostValue();
        if (!$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        if (!$seller) {
            $this->messageManager->addErrorMessage(__('Invalid seller account'));
            return $this->_redirect('sellerinfo/index/paymentinfo');
        }
        if ($data) {
            $dataTemp = $data;
            $dataTemp['cs_cc_rest_api_secret_key'] = 'fake';
            $dataTemp['cs_pt_rest_api_key'] = 'fake';
            $dataTemp['cs_tsa_pt_rest_api_secret_key'] = 'fake';
            $this->customer->setSellerPaymentFormData($dataTemp);
            $validationFlag = true;
            try {
                foreach ($data as $dk => $dv) {
                    if (strpos($dk, 'cs_cc') === false || strpos($dk, 'changed_') !== false) continue;
                    if (!trim($dv)) {
                        $validationFlag = false;
                        break;
                    }
                }
                foreach ($data as $dk => $dv) {
                    if (strpos($dk, 'cs_pt') === false || strpos($dk, 'changed_') !== false) continue;
                    if (!trim($dv)) {
                        $validationFlag = false;
                        break;
                    }
                }
                foreach ($data as $dk => $dv) {
                    if (strpos($dk, 'cs_tsa') === false || strpos($dk, 'changed_') !== false) continue;
                    if (!trim($dv)) {
                        $validationFlag = false;
                        break;
                    }
                }
                if (!$validationFlag) {
                    $this->messageManager->addErrorMessage(__('All fields are required'));
                    return $this->_redirect('sellerinfo/index/paymentinfo');
                }

                if ($data['changed_cs_cc_rest_api_secret_key'] && $data['cs_cc_rest_api_secret_key'] != 'fake') {
                    $data['cs_cc_rest_api_secret_key'] = $this->encryptor->encrypt($data['cs_cc_rest_api_secret_key']);
                } else {
                    unset($data['cs_cc_rest_api_secret_key']);
                }
                if ($data['changed_cs_pt_rest_api_key'] && $data['cs_pt_rest_api_key'] != 'fake') {
                    $data['cs_pt_rest_api_key'] = $this->encryptor->encrypt($data['cs_pt_rest_api_key']);
                } else {
                    unset($data['cs_pt_rest_api_key']);
                }
                if ($data['changed_cs_tsa_pt_rest_api_secret_key'] && $data['cs_tsa_pt_rest_api_secret_key'] != 'fake') {
                    $data['cs_tsa_pt_rest_api_secret_key'] = $this->encryptor->encrypt($data['cs_tsa_pt_rest_api_secret_key']);
                } else {
                    unset($data['cs_tsa_pt_rest_api_secret_key']);
                }
                $sellerStore = $this->store->load($seller);
                foreach ($data as $dk => $dv) {
                    if (strpos($dk, 'cs_cc_') === false) continue;
                    $sellerStore->setData($dk, trim($dv));
                }
                foreach ($data as $dk => $dv) {
                    if (strpos($dk, 'cs_pt_') === false) continue;
                    $sellerStore->setData($dk, trim($dv));
                }
                foreach ($data as $dk => $dv) {
                    if (strpos($dk, 'cs_tsa_') === false) continue;
                    $sellerStore->setData($dk, trim($dv));
                }
                $sellerStore->save();

                $this->messageManager->addSuccessMessage(__('Payment details saved successfully'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the details'));
            }
        }

        return $this->_redirect('sellerinfo/index/paymentinfo');
    }
}
