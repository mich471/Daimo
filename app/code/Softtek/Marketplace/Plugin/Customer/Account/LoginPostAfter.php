<?php
namespace Softtek\Marketplace\Plugin\Customer\Account;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Controller\Account\LoginPost;
use Purpletree\Marketplace\Model\ResourceModel\Seller;

class LoginPostAfter
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
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
     * @param ManagerInterface $managerInterface
     * @param Seller $storeDetails
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        ManagerInterface $managerInterface,
        Seller $storeDetails
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->managerInterface = $managerInterface;
        $this->storeDetails = $storeDetails;
    }

    public function afterExecute(
        LoginPost $subject, $result
    )
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $result;
        }
        $isSeller = $this->customerSession->getCustomer()->getIsSeller();
        $sellerData = $this->storeDetails->getStoreDetails($this->customerSession->getCustomer()->getId());
        $sellerStatusId = $sellerData["status_id"];
        if ($isSeller != 1) {
            return $result;
        }
        if ($sellerStatusId == 0 || $sellerStatusId == 2 || $sellerStatusId == 4) {
            $this->customerSession->logout();
            $result->setPath('customer/account/login');
            $this->managerInterface->addErrorMessage(__('Your registration request is pending approval. If more than 72 hours have passed, please request support.'));
            return $result;
        }
        if ($sellerStatusId == 3) {
            $result->setPath('marketplace/index/becomeseller');
            $this->managerInterface->addSuccessMessage(__('Please fill in the data of your store to continue the process'));
            return $result;
        }

        return $result;
    }
}
