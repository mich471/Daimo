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

namespace Plumrocket\GDPR\Ui\DataProvider\Checkbox\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class CheckboxDataProvider extends AbstractDataProvider
{
    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Checkbox\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Ui\DataProvider\Modifier\PoolInterface
     */
    private $pool;

    /**
     * CheckboxDataProvider constructor.
     *
     * @param string                                                          $name
     * @param string                                                          $primaryFieldName
     * @param string                                                          $requestFieldName
     * @param \Plumrocket\GDPR\Model\ResourceModel\Checkbox\CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\Modifier\PoolInterface                 $pool
     * @param array                                                           $meta
     * @param array                                                           $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Plumrocket\GDPR\Model\ResourceModel\Checkbox\CollectionFactory $collectionFactory,
        \Magento\Ui\DataProvider\Modifier\PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->pool = $pool;
    }

    /**
     * @return \Plumrocket\GDPR\Model\ResourceModel\Checkbox\Collection
     */
    public function getCollection()
    {
        if (! $this->collection) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->addAttributeToSelect('*');
        }

        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->data = $this->loadData();

        /** @var ModifierInterface $modifier */
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

        /** @var ModifierInterface $modifier */
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
        foreach ($this->getCollection()->getItems() as $checkbox) {
            $data[$checkbox->getId()] = $checkbox->getData();
        }

        return $data;
    }
}
