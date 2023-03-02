<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\CollectionFactory;

/**
 * @since 3.2.0
 */
class AdminRemovalRequest extends AbstractDataProvider
{

    /**
     * @param string                                                                       $name
     * @param string                                                                       $primaryFieldName
     * @param string                                                                       $requestFieldName
     * @param \Plumrocket\DataPrivacy\Model\ResourceModel\RemovalRequest\CollectionFactory $collectionFactory
     * @param array                                                                        $meta
     * @param array                                                                        $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return [
            // return the form data here
        ];
    }
}
