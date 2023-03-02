<?php
/**
 * @package     Plumrocket_magento2.3.6
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Guest;

use Magento\Framework\UrlInterface;

class GetPrivacyCenterUrl
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function execute(string $token): string
    {
        return $this->urlBuilder->getUrl('prgdpr/account/index', ['token' => $token]);
    }
}
