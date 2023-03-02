<?php
namespace Softtek\Marketplace\Model\Order;

use Magento\Sales\Model\Order\Config as OrderConfig;

/**
 * Order configuration model
 *
 * @api
 * @since 100.0.2
 */
class Config extends OrderConfig
{
    /**
     * @var array
     */
    protected $maskStatusesMapping = [
        \Magento\Framework\App\Area::AREA_FRONTEND => [
            \Magento\Sales\Model\Order::STATUS_FRAUD => \Magento\Sales\Model\Order::STATUS_FRAUD,
            \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW => \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW
        ]
    ];
}
