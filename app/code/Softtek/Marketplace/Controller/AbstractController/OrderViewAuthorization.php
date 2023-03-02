<?php
namespace Softtek\Marketplace\Controller\AbstractController;

use Magento\Sales\Controller\AbstractController\OrderViewAuthorization as AbstractControllerOrderViewAuthorization;

class OrderViewAuthorization extends AbstractControllerOrderViewAuthorization
{
    /**
     * {@inheritdoc}
     */
    public function canView(\Magento\Sales\Model\Order $order)
    {
        return true;
    }
}
