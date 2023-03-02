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

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Plumrocket\CookieConsent\Model\ResourceModel\Category\Collection as CategoryCollection;

/**
 * @method CategoryCollection getCollection()
 * @since 1.0.0
 */
abstract class DataProvider extends AbstractDataProvider
{
    /**
     * @var \Magento\Ui\DataProvider\Modifier\PoolInterface
     */
    private $pool;

    /**
     * AbstractFormDataProvider constructor.
     *
     * @param string                                          $name
     * @param string                                          $primaryFieldName
     * @param string                                          $requestFieldName
     * @param \Magento\Ui\DataProvider\Modifier\PoolInterface $pool
     * @param array                                           $meta
     * @param array                                           $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->data = $this->loadData();

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * @return array
     */
    private function loadData()
    {
        $data = [];
        foreach ($this->getCollection()->getItems() as $item) {
            $data[$item->getId()] = $item->getData();
        }

        return $data;
    }
}
