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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Consent\Location;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Plumrocket\GDPR\Model\ResourceModel\Consent\Location\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->primaryFieldName = 'location_id';
        $this->requestFieldName = 'location_id';
        $meta = $this->getPreparedMetadata($meta);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    private function getPreparedMetadata($meta)
    {
        $meta['general']['children']['name']['arguments']['data']['config']['notice'] = $this->getNoticeForName();
        $meta['general']['children']['location_key']['arguments']['data']['config']['notice']
            = $this->getNoticeForLocationKey();
        $meta['general']['children']['description']['arguments']['data']['config']['notice']
            = $this->getNoticeForDescription();

        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var \Plumrocket\GDPR\Model\Consent\Location[] $items */
        $items = $this->collection->getItems();

        /** @var \Magento\Cms\Model\Block $location */
        foreach ($items as $location) {
            $this->loadedData[$location->getId()] = $location->getData();
        }

        $data = $this->dataPersistor->get('consent_location');

        if (! empty($data)) {
            $location = $this->collection->getNewEmptyItem();
            $location->setData($data);
            $this->loadedData[$location->getId()] = $location->getData();
            $this->dataPersistor->clear('consent_location');
        }

        return $this->loadedData;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getNoticeForName()
    {
        return __('The name of your custom consent location.');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getNoticeForLocationKey()
    {
        return __(
            'Unique identificator of your custom consent location.'
            . ' Example: "promo_page", "myPopupForm", "login_form".'
            . ' You will need this key to display consent checkboxes manually.'
            . ' Please read our developer\'s guide for more info.'
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getNoticeForDescription()
    {
        return __(
            'Optional field for Admin users only.'
            . ' Useful to describe custom checkbox location'
            . ' (such as "New Year\'s promo landing page") or add some internal notes.'
        );
    }
}
