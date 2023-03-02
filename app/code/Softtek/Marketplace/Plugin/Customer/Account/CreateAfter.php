<?php
namespace Softtek\Marketplace\Plugin\Customer\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Softtek\Marketplace\Helper\Data;
use Magento\Customer\Controller\Account\Create;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Magento\Framework\Message\ManagerInterface;

class CreateAfter
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
     * @var Session
     */
    protected $session;

    /**
     * @var Seller
     */
    protected $storeDetails;

    /**
     * @var ManagerInterface
     */
    protected $managerInterface;

    /**
     * Plugin constructor.
     *
     * @param UrlInterface $urlInterface
     * @param Data $stmHelper
     * @param Session $session
     * @param Seller $storeDetails
     * @param ManagerInterface $managerInterface
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        UrlInterface $urlInterface,
        Data $stmHelper,
        Session $session,
        Seller $storeDetails,
        ManagerInterface $managerInterface
    )
    {
        $this->urlInterface = $urlInterface;
        $this->stmHelper = $stmHelper;
        $this->session = $session;
        $this->storeDetails = $storeDetails;
        $this->managerInterface = $managerInterface;
    }

    /**
     * After plugin to redirect user if it is logged-in
     *
     * @param Create $subject
     * @return Object
     */
    public function afterExecute(
        Create $subject, $result
    ) {
        if (!$this->stmHelper->isEnabled()) {
            return $result;
        }

        if (!$this->session->isLoggedIn()) {
            return $result;
        }

        $isSeller = $this->session->getCustomer()->getIsSeller();
        if (!$isSeller) {
            $this->managerInterface->addErrorMessage(__('To create a seller account you must log out, enter the Sell page and register with an email that does not exist in the store'));
            $result->setPath('customer/account');
            return $result;
        }
        $sellerData = $this->storeDetails->getStoreDetails($this->session->getCustomer()->getId());
        $sellerStatusId = $sellerData["status_id"];

        $params = $subject->getRequest()->getParams();
        if (!isset($params['ut'])) {
            return $result;
        }
        if ($params['ut'] != 'seller') {
            return $result;
        }

        $result->setPath('marketplace/index/becomeseller');

        return $result;
    }
}
