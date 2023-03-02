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

namespace Plumrocket\CookieConsent\Ui\DataProvider\Cookie\Item\Form;

use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Collection as CookieCollection;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\CollectionFactory;
use Plumrocket\CookieConsent\Ui\DataProvider\AbstractForm\DataProvider as AbstractFormDataProvider;

/**
 * @method CookieCollection getCollection()
 * @since 1.0.0
 */
class DataProvider extends AbstractFormDataProvider
{
    /**
     * CheckboxDataProvider constructor.
     *
     * @param string                                                                   $name
     * @param string                                                                   $primaryFieldName
     * @param string                                                                   $requestFieldName
     * @param \Magento\Ui\DataProvider\Modifier\PoolInterface                          $pool
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\CollectionFactory $collectionFactory
     * @param array                                                                    $meta
     * @param array                                                                    $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $pool, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
