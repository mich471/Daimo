<?php

/**
 * Config Provider Class
 *
 * @package Softtek_Payment
 * @author Jorge Serena <jorge.serena@softtek.com>
 * @copyright © Softtek. All rights reserved.
 */

namespace Softtek\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Checkout\Model\Session;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Magento\Catalog\Model\ProductRepository;

class Data extends AbstractHelper
{
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var integer
     */
    protected $sellerId;

    /**
     * @var integer
     */
    protected $sellerStore;

    /**
     * @var Seller
     */
    protected $storeDetails;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param Session $checkoutSession
     * @param Seller $storeDetails
     * @param ProductRepository $productRepository
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        Session $checkoutSession,
        Seller $storeDetails,
        ProductRepository $productRepository
    ) {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->checkoutSession = $checkoutSession;
        $this->storeDetails = $storeDetails;
        $this->productRepository = $productRepository;
    }

    /**
     * @param ScopeConfigInterface
     * @return string
     */
    public function getApiKey()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $storeDetails['cs_cc_rest_api_key'];
        }

        return '';
    }

    /**
     * @param ScopeConfigInterface
     * @return string
     */
    public function getApiSecret()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $this->encryptor->decrypt($storeDetails['cs_cc_rest_api_secret_key']);
        }

        return '';
    }

    /**
     * @param ScopeConfigInterface
     * @return string
     */
    public function getMerchantId()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $storeDetails['cs_cc_merchant_id'];
        }

        return '';
    }

    /**
     * @param ScopeConfigInterface
     * @return string
     */
    public function getOrgId()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $storeDetails['cs_cc_org_id'];
        }

        return '';
    }

    /**
     * @param ScopeConfigInterface
     * @return string
     */
    public function getProfileId()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $storeDetails['cs_cc_profile_id'];
        }

        return '';
    }

    /**
     * @param ScopeConfigInterface
     * @return string
     */
    public function getKeyAlias()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $storeDetails['cs_cc_key_alias'];
        }

        return '';
    }

    /**
     * @param ScopeConfigInterface
     * @return string
     */
    public function getKeyPass()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $storeDetails['cs_cc_key_pass'];
        }

        return '';
    }

    /**
     * @param ScopeConfigInterface
     * @return string
     */
    public function getKeyFileName()
    {
        if ($storeDetails = $this->getSellerStore()) {
            return $storeDetails['cs_cc_key_filename'];
        }

        return '';
    }

    /**
     * @param ScopeConfigInterface
     * @return string
     */
    public function getRunEnv($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            'payment/softtek_payment/envioment',
            $scope
        );
    }

    /**
     * @return integer
     */
    protected function getSellerId()
    {
        if (is_null($this->sellerId)) {
            foreach ($this->checkoutSession->getQuote()->getAllItems() as $item) {
                if (is_null($this->sellerId)) {
                    $product = $this->productRepository->getById($item->getProductId());
                    $this->sellerId = $product->getSellerId();
                    break;
                }
            }
        }

        return $this->sellerId;
    }

    /**
     * @return integer
     */
    protected function getSellerStore()
    {
        if (is_null($this->sellerStore)) {
            $this->sellerStore = $this->storeDetails->getStoreDetails($this->getSellerId());
        }

        return $this->sellerStore;
    }
}
