<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacy\Ui\Component\Listing\RemovalRequest;

use Magento\Customer\Model\ResourceModel\Address\Grid\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Custom DataProvider for removal requests.
 *
 * @since 3.2.0
 */
class DataProvider extends AbstractDataProvider
{

    /**
     * @var RequestInterface $request,
     */
    private $request;

    /**
     * @param string                                                                            $name
     * @param string                                                                            $primaryFieldName
     * @param string                                                                            $requestFieldName
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\Grid\CollectionFactory $collectionFactory
     * @param RequestInterface                                                                  $request
     * @param array                                                                             $meta
     * @param array                                                                             $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\Grid\CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
    }

    /**
     * Add country key for default billing/shipping blocks on customer addresses tab
     *
     * @return array
     */
    public function getData(): array
    {
        /** @var \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\Grid\Collection $collection */
        $collection = $this->getCollection();
        $data['items'] = [];
        if ($this->request->getParam('parent_id')) {
            $collection->addFieldToFilter('customer_id', $this->request->getParam('parent_id'));
            $data = $collection->toArray();
        }

        return $data;
    }
}
