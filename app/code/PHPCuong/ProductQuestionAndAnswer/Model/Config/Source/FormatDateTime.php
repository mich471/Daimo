<?php

/**
 * @Author: Ngo Quang Cuong
 * @Date:   2017-10-15 05:37:28
 * @Last Modified by:   https://www.facebook.com/giaphugroupcom
 * @Last Modified time: 2017-10-21 17:07:08
 */

namespace PHPCuong\ProductQuestionAndAnswer\Model\Config\Source;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;

class FormatDateTime
{
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @param TimezoneInterface $timezone
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        TimezoneInterface $timezone,
        StoreManagerInterface $storeManager
    ) {
        $this->timezone = $timezone;
        $this->storeManager = $storeManager;
    }

    /**
    * Format the datetime
    *
    * @param string $value
    * @param string $format
    * @return string
    */
    /**
    public function getDateTime($value, $format = 'M d, Y g:i:s A')
    {
    if (!empty($value)) {
    $date = $this->timezone->date(new \DateTime($value));
    $configTimezone = $this->timezone->getConfigTimezone('store', $this->storeManager->getStore()->getStoreId());
    if (isset($configTimezone) && !$configTimezone) {
    $date = new \DateTime($value);
    }
    return $date->format($format);
    }
    return $value;
    }
     **/

    public function getDateTime($value, $format = 'dd/MM/YYYY')
    {
        if (!empty($value)) {
            $date = $this->timezone->date(new \DateTime($value));
            $configTimezone = $this->timezone->getConfigTimezone('store', $this->storeManager->getStore()->getStoreId());
            if (isset($configTimezone) && !$configTimezone) {
                $date = new \DateTime($value);
            }
            return $date->format($format);
        }
        return $value;
    }

    /**
    * Format the created at
    *
    * @param string $date
    * @return string
    */
    public function formatCreatedAt($date)
    {
        return date("d/", strtotime($date)) . __(date("F", strtotime($date))) . date("/Y", strtotime($date));
    }
    public function formatCreatedAtWithTime($date)
    {
        return date("d", strtotime($date)).' '. __('of').' '. __(date("F", strtotime($date))).' '. __('of').' '.date("Y", strtotime($date)).' '.__('at').' '.$this->getDateTime($date, 'H:i');
//        return $this->getDateTime($date, 'd M Y H:i');
    }
}
