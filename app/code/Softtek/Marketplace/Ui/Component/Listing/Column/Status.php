<?php
/**
 * Purpletree_Marketplace Status
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Softtek\Marketplace\Ui\Component\Listing\Column;

use Purpletree\Marketplace\Ui\Component\Listing\Column\Status as MarketplaceStatus;

/**
 * Class Status
 */
class Status extends MarketplaceStatus
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['seller_status_id'])) {
                    $status = $item['seller_status_id'];
                    switch ($status) {
                        case 0:
                            $item['seller_status_id'] =  __('Disabled');
                            break;
                        case 1:
                            $item['seller_status_id'] = __('Enabled');
                            break;
                        case 2:
                            $item['seller_status_id'] = __('Waiting approval to receive data');
                            break;
                        case 3:
                            $item['seller_status_id'] = __('Approved to receive data');
                            break;
                        case 4:
                            $item['seller_status_id'] = __('Waiting approval to sell');
                            break;
                    }
                }
            }
        }
        return $dataSource;
    }
}
