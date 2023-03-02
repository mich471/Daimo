<?php
/**
 * @package     Plumrocket_magento2.3.6
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Archive;

class Zip extends \Magento\Framework\Archive\Zip
{
    /**
     * @inheritDoc
     */
    public function pack($source, $destination)
    {
        $zip = new \ZipArchive();
        $zip->open($destination, \ZipArchive::CREATE);
        $zip->addFile($source, basename($source));
        $zip->close();
        return $destination;
    }
}