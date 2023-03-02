<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Account\Data;

use Plumrocket\Base\Model\Utils\Config;

class Anonymizer
{
    private const XML_PATH_KEY = 'prgdpr/removal_settings/anonymization_key';

    /**
     * @var \Plumrocket\Base\Model\Utils\Config
     */
    private $configUtils;

    /**
     * @param \Plumrocket\Base\Model\Utils\Config $configUtils
     */
    public function __construct(Config $configUtils)
    {

        $this->configUtils = $configUtils;
    }

    /**
     * Get anonymization key.
     *
     * @return string
     */
    public function getKey(): string
    {
        $key = trim($this->configUtils->getConfig(self::XML_PATH_KEY));

        if (! $key) {
            $key = 'xxxx';
        }

        return $key;
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function getString(int $customerId): string
    {
        return $this->getKey() . '-' . $customerId;
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function getEmail(int $customerId): string
    {
        return $this->getString($customerId) . '@example.com';
    }

    /**
     * @param $data
     * @param int $customerId
     * @return array
     */
    public function getData($data, int $customerId): array
    {
        $dataAnonymized = [];

        if (! empty($data) && is_array($data)) {
            foreach ($data as $field => $value) {
                switch ($value) {
                    case 'anonymousEmail':
                        $dataAnonymized[$field] = $this->getEmail($customerId);
                        break;
                    case 'anonymousString':
                        $dataAnonymized[$field] = $this->getString($customerId);
                        break;
                    default:
                        $dataAnonymized[$field] = $value;
                }
            }
        }

        return $dataAnonymized;
    }
}
