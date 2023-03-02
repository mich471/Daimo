<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Helper\Guest;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var array
     */
    protected $dataModels = [
        [
            'class' => \Magento\Customer\Model\Customer::class,
            'fieldName' => 'email'
        ],
        [
            'class' => \Magento\Newsletter\Model\Subscriber::class,
            'fieldName' => 'subscriber_email'
        ],
        [
            'class' => \Magento\Sales\Model\Order::class,
            'fieldName' => 'customer_email'
        ],
        [
            'class' => \Magento\Sales\Model\Order\Address::class,
            'fieldName' => 'email'
        ],
        [
            'class' => \Magento\Quote\Model\Quote::class,
            'fieldName' => 'customer_email'
        ],
        [
            'class' => \Magento\Quote\Model\Quote\Address::class,
            'fieldName' => 'email'
        ]
    ];

    /**
     * @var \Plumrocket\GDPR\Model\Loader
     */
    private $loader;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $customerSession;

    /**
     * Data constructor.
     *
     * @param \Plumrocket\GDPR\Model\Loader $loader
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Session\SessionManagerInterface $customerSession
     */
    public function __construct(
        \Plumrocket\GDPR\Model\Loader $loader,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $customerSession
    ) {
        parent::__construct($context);
        $this->loader = $loader;
        $this->customerSession = $customerSession;
    }

    /**
     * @param $email
     * @return bool
     */
    public function emailExistInDb($email)
    {
        foreach ($this->dataModels as $model) {
            $externalModel = $this->loader->createModel($model['class'])
                ->getCollection()
                ->addFieldToFilter($model['fieldName'], $email);

            if ($externalModel->getSize()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isDashboardForGuest()
    {
        return ! $this->customerSession->isLoggedIn();
    }
}
