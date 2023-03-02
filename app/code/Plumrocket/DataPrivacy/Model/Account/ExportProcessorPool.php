<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Account;

use Plumrocket\GDPR\Api\DataExportProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Model\Account\Processor;

/**
 * Return list of export processors.
 *
 * @since 3.1.0
 */
class ExportProcessorPool
{

    /**
     * @var \Plumrocket\GDPR\Model\Account\Processor
     */
    private $processor;

    /**
     * @param \Plumrocket\GDPR\Model\Account\Processor $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @return \Plumrocket\DataPrivacyApi\Api\DataExportProcessorInterface[]|Object[]
     */
    public function getList(): array
    {
        $exportProcessors = [];
        foreach ($this->processor->getAllProcessors() as $processorData) {
            $processor = $processorData['processor'];

            if (method_exists($processor, 'getFileName')
                && ($processor instanceof DataExportProcessorInterface
                    || $processor instanceof DataProcessorInterface
                    || method_exists($processor, 'exportCustomerData')
                    || method_exists($processor, 'exportGuestData')
                    || method_exists($processor, 'export')
                )
            ) {
                $exportProcessors[] = $processor;
            }
        }

        return $exportProcessors;
    }
}
