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

namespace Plumrocket\CookieConsent\Ui\DataProvider\Cookie\Item\Listing;

use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Grid\CollectionFactory;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Grid\Collection;
use Plumrocket\CookieConsent\Ui\DataProvider\AbstractListing\DataProvider as AbstractListingDataProvider;

/**
 * @since 1.0.0
 */
class DataProvider extends AbstractListingDataProvider
{
    /**
     * DataProvider constructor.
     *
     * @param string                                                                      $name
     * @param string                                                                      $primaryFieldName
     * @param string                                                                      $requestFieldName
     * @param \Plumrocket\CookieConsent\Model\ResourceModel\Cookie\Grid\CollectionFactory $collectionFactory
     * @param array                                                                       $addFieldStrategies
     * @param array                                                                       $addFilterStrategies
     * @param array                                                                       $meta
     * @param array                                                                       $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
    }

    /**
     *
     * @param string $field
     * @param string $direction
     * @return void
     */
    public function addOrder($field, $direction)
    {
        if ($field === Collection::DOMAIN_LABEL) {
            $this->getCollection()->getSelect()->order($field . ' ' . $direction);
        } else {
            parent::addOrder($field, $direction);
        }
    }
}
