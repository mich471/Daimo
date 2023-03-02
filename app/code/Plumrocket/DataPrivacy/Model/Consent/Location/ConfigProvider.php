<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Location;

use Magento\Framework\Config\Reader\Filesystem;

/**
 * @since 3.1.0
 */
class ConfigProvider
{

    /**
     * @var \Magento\Framework\Config\Reader\Filesystem
     */
    private $reader;

    /**
     * @var array
     */
    private $data;

    public function __construct(Filesystem $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Get config value by key
     *
     * @param string|null $path
     * @param mixed       $default
     * @return array|mixed|null
     */
    public function get(?string $path = null, $default = null)
    {
        $data = $this->read();
        if ($path === null) {
            return $data;
        }
        $keys = explode('/', $path);
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return $default;
            }
        }
        return $data;
    }

    private function read(): array
    {
        if (null === $this->data) {
            $this->data = $this->reader->read();
        }

        return $this->data;
    }
}
