<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Location;

use Plumrocket\DataPrivacyApi\Api\ConsentLocationTypeInterface;

class Type implements ConsentLocationTypeInterface, \Plumrocket\GDPR\Api\ConsentLocationTypeInterface
{
    /**
     * Retrieve list of consent location types
     *
     * @return array
     */
    public function getList() : array
    {
        return [
            ConsentLocationTypeInterface::TYPE_DEFAULT    => __('Default'),
            ConsentLocationTypeInterface::TYPE_PLUMROCKET => __('Plumrocket'),
            ConsentLocationTypeInterface::TYPE_CUSTOM     => __('Custom'),
        ];
    }

    /**
     * Retrieve list of consent location types
     *
     * @return array
     */
    public function toOptionArray() : array
    {
        return array_map(static function ($label) {
            return ['label' => $label, 'value' => []];
        }, $this->getList());
    }

    /**
     * @param int $type
     * @return bool
     */
    public function isSystem(int $type) : bool
    {
        return in_array(
            $type,
            [
                ConsentLocationTypeInterface::TYPE_DEFAULT,
                ConsentLocationTypeInterface::TYPE_PLUMROCKET
            ],
            true
        );
    }
}
