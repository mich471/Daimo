<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Ui\Component\Listing\RemovalRequest\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @since 3.2.0
 */
class Actions extends Column
{

    public const REMOVAL_REQUEST_URL_PATH_CANCEL = 'pr_data_privacy/removalRequest/cancel';

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['request_id']) && $item['status'] === 'pending') {
                    if ('json' === $this->getData('config/response')) {
                        $item[$this->getData('name')] = [
                            'cancel' => [
                                'href' => $this->context->getUrl(
                                    self::REMOVAL_REQUEST_URL_PATH_CANCEL,
                                    [
                                        'request_id' => $item['request_id'],
                                        'responseType' => $this->getData('config/response')
                                    ]
                                ),
                                'isAjax' => true,
                                'label' => __('Cancel'),
                                'confirm' => [
                                    'title' => __('Are You Sure?'),
                                    'message' => __('Are you sure you want to cancel this removal request?')
                                ]
                            ]
                        ];
                    } else {
                        $item[$this->getData('name')] = [
                            'cancel' => [
                                'href' => $this->context->getUrl(
                                    self::REMOVAL_REQUEST_URL_PATH_CANCEL,
                                    ['request_id' => $item['request_id']]
                                ),
                                'label' => __('Cancel'),
                                'confirm' => [
                                    'title' => __('Are You Sure?'),
                                    'message' => __('Are you sure you want to cancel this removal request?')
                                ]
                            ]
                        ];
                    }
                }
            }
        }
        return $dataSource;
    }
}
