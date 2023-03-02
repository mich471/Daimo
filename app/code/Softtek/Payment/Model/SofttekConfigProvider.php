<?php

/**
 * Config Provider Class
 *
 * @package Softtek_Payment
 * @author Paul Soberanes <paul.soberanes@softtek.com>
 * @copyright © Softtek. All rights reserved.
 */

namespace Softtek\Payment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class SofttekConfigProvider implements ConfigProviderInterface
{
    const CODE = 'calculadora';

    public function __construct(
        \Softtek\Payment\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function getConfig()
    {
        $urlCyber = "https://h.online-metrix.net/fp/tags.js?org_id=" . $this->_helper->getOrgId() . "&session_id=" . $this->_helper->getMerchantId();
        return [
            'payment' => [
                self::CODE => [
                    "version" => [
                        "url" => $urlCyber,
                    ],
                ]
            ]
        ];
    }
}
