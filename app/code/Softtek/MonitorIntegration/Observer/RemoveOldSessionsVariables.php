<?php
/**
 * Softtek Custom Location Module
 *
 * @package Softtek_CartLocation
 * @author Gustavo Casas <gustavo.casas@softtek.com>
 * @copyright Softtek 2020
 */

namespace Softtek\MonitorIntegration\Observer;

use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Softtek\CartLocation\Helper\Data as CookieHelper;

class RemoveOldSessionsVariables implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        //To avoid conflicts, we remove the information from the session after a new product is addedd to the cart.
        $stockInfoSession = $this->customerSession->getStockInfo();
        if ($stockInfoSession) {
            $this->customerSession->unsStockInfo();
        }
    }
}
