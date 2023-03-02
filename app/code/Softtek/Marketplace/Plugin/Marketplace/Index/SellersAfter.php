<?php
namespace Softtek\Marketplace\Plugin\Marketplace\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;
use Purpletree\Marketplace\Controller\Index\Sellers;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Magento\Framework\App\Response\Http as responseHttp;
use Magento\Framework\UrlInterface;

class SellersAfter
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ManagerInterface
     */
    protected $managerInterface;

    /**
     * @var Seller
     */
    protected $storeDetails;

    /**
     * @var responseHttp
     */
    protected $response;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
     * @param ManagerInterface $managerInterface
     * @param Seller $storeDetails
     * @param responseHttp $response
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        ManagerInterface $managerInterface,
        Seller $storeDetails,
        responseHttp $response,
        UrlInterface $urlBuilder
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->managerInterface = $managerInterface;
        $this->storeDetails = $storeDetails;
        $this->response = $response;
        $this->urlBuilder = $urlBuilder;
    }

    public function afterExecute(
        Sellers $subject, $result
    )
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $result;
        }
        $isSeller = $this->customerSession->getCustomer()->getIsSeller();
        $sellerData = $this->storeDetails->getStoreDetails($this->customerSession->getCustomer()->getId());
        if ($isSeller != 1) {
            return $result;
        }
        if ($sellerData["store_url"] == "") {
            $url = $this->urlBuilder->getUrl('marketplace/index/becomeseller');
            $this->response->setRedirect($url);
            $this->managerInterface->addSuccessMessage(__('Please fill in the data of your store to continue the process'));
            return $result;
        }

        return $result;
    }
}
