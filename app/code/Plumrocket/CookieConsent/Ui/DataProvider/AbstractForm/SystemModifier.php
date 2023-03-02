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
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Ui\DataProvider\AbstractForm;

use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Plumrocket\CookieConsent\Ui\Locator\LocatorInterface;

/**
 * @since 1.0.0
 */
abstract class SystemModifier implements ModifierInterface
{
    const KEY_SUBMIT_URL = 'submit_url';

    /**
     * @var \Plumrocket\CookieConsent\Ui\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    protected $actionUrls = [];

    /**
     * SystemModifier constructor.
     *
     * @param \Plumrocket\CookieConsent\Ui\Locator\LocatorInterface $locator
     * @param \Magento\Framework\UrlInterface                       $urlBuilder
     * @param array                                                 $actionUrls
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        array $actionUrls = []
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->actionUrls = array_replace_recursive($this->actionUrls, $actionUrls);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $model = $this->locator->getModel();

        $parameters = [
            'id' => $model->getId(),
            'store' => $model->getStoreId(),
        ];

        $submitUrl = $this->urlBuilder->getUrl($this->actionUrls[self::KEY_SUBMIT_URL], $parameters);

        return array_replace_recursive(
            $data,
            [
                'config' => [
                    self::KEY_SUBMIT_URL => $submitUrl,
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
