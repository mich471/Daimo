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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Ui\DataProvider\Checkbox\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class System implements ModifierInterface
{
    const KEY_SUBMIT_URL = 'submit_url';

    /**
     * @var \Plumrocket\GDPR\Model\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $checkboxUrls = [
        self::KEY_SUBMIT_URL => 'prgdpr/consent_checkbox/save',
    ];

    /**
     * System constructor.
     *
     * @param \Plumrocket\GDPR\Model\Locator\LocatorInterface $locator
     * @param \Magento\Framework\UrlInterface                 $urlBuilder
     * @param array                                           $productUrls
     */
    public function __construct(
        \Plumrocket\GDPR\Model\Locator\LocatorInterface $locator,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $productUrls = []
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->checkboxUrls = array_replace_recursive($this->checkboxUrls, $productUrls);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $model = $this->locator->getCheckbox();

        $parameters = [
            'id' => $model->getId(),
            'store' => $model->getStoreId(),
        ];

        $submitUrl = $this->urlBuilder->getUrl($this->checkboxUrls[self::KEY_SUBMIT_URL], $parameters);

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
