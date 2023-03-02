<?php
namespace Softtek\Marketplace\Observer\Customer\Account;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session;
use Softtek\Marketplace\Helper\Data;


class CreatePostBefore implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $stmHelper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Plugin constructor.
     *
     * @param Data $stmHelper
     * @param Session $session
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Data $stmHelper,
        Session $session
    )
    {
        $this->stmHelper = $stmHelper;
        $this->session = $session;
    }

    /**
     * Observer execute
     *
     * @param Observer $observer
     * @return Observer
     * @throws CouldNotSaveException
     */
    public function execute(Observer $observer)
    {
        if (!$this->stmHelper->isEnabled()) {
            return $this;
        }

        $controller = $observer->getControllerAction();
        $params = $controller->getRequest()->getParams();
        if (!isset($params['ut'])) {
            return $this;
        }
        if ($params['ut'] != 'seller') {
            return $this;
        }

        $this->session->setSellerFormData($params);

        return $this;
    }
}
