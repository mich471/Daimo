<?php
namespace Foxsea\Paghiper\Model\Config\Source\Order\Status;

use Magento\Sales\Model\Config\Source\Order\Status;
use Magento\Sales\Model\Order;

class Pendingpayment extends Status {

    /**
    * @var string[]
    */
    protected $_stateStatuses = [
        Order::STATE_PENDING_PAYMENT,
        Order::STATE_PAYMENT_REVIEW
    ];
}
