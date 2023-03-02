<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Account;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\DataPrivacy\Model\Archive\Zip;
use Plumrocket\DataPrivacyApi\Api\DataExportProcessorInterface;

/**
 * Create zip archive with csv files.
 *
 * For each data export processor we create their own csv file, unless they return null instead of customer data.
 *
 * @since 3.1.0
 */
class Exporter implements DataExportProcessorInterface
{

    /**
     * @var \Plumrocket\DataPrivacy\Model\Account\ExportProcessorPool
     */
    private $exporterPool;

    /**
     * @var \Plumrocket\DataPrivacy\Model\Archive\Zip
     */
    private $zip;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $file;

    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csvWriter;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    private $dir;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime               $dateTime
     * @param \Magento\Framework\App\Response\Http\FileFactory          $fileFactory
     * @param \Magento\Framework\Filesystem\Driver\File                 $file
     * @param \Magento\Framework\File\Csv                               $csvWriter
     * @param \Plumrocket\DataPrivacy\Model\Archive\Zip                 $zip
     * @param \Magento\Framework\Filesystem\DirectoryList               $dir
     * @param \Plumrocket\DataPrivacy\Model\Account\ExportProcessorPool $exporterPool
     */
    public function __construct(
        DateTime $dateTime,
        FileFactory $fileFactory,
        File $file,
        Csv $csvWriter,
        Zip $zip,
        DirectoryList $dir,
        ExportProcessorPool $exporterPool
    ) {
        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
        $this->file = $file;
        $this->csvWriter = $csvWriter;
        $this->dir = $dir;
        $this->zip = $zip;
        $this->exporterPool = $exporterPool;
    }

    /**
     * @inheritDoc
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $this->export($customer, 'exportCustomerData');
        return null;
    }

    /**
     * @inheritDoc
     */
    public function exportGuestData(string $email): ?array
    {
        $this->export($email, 'exportGuestData');
        return null;
    }

    /**
     * This function return .zip file with customer data.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $identifier
     * @param string                                              $methodName
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    private function export($identifier, string $methodName): void
    {
        $dir = $this->dir->getPath('tmp');
        $dateTime = $this->getDateStamp();
        $zipFileName = $this->getFileName($dateTime) . '.zip';

        foreach ($this->exporterPool->getList() as $processor) {
            $dataExport = $this->runExportProcessor($processor, $identifier, $methodName);
            if (! $dataExport) {
                continue;
            }

            $fileName = $dir . DIRECTORY_SEPARATOR . $processor->getFileName($dateTime) . '.csv';

            $this->createFile($fileName, $dataExport);
            $this->zip->pack(
                $fileName,
                $dir . DIRECTORY_SEPARATOR . $zipFileName
            );
            $this->deleteFile($fileName);
        }

        $this->fileFactory->create(
            $zipFileName,
            [
                'type' => 'filename',
                'value' => $zipFileName,
                'rm' => true,
            ],
            'tmp',
            'zip',
            null
        );
    }

    /**
     * @param \Plumrocket\DataPrivacyApi\Api\DataExportProcessorInterface|Object $processor
     * @param \Magento\Customer\Api\Data\CustomerInterface|string                $identifier customer or email
     * @param string $methodName
     * @return array|null
     * @throws \Exception
     */
    private function runExportProcessor($processor, $identifier, string $methodName): ?array
    {
        if ($methodName === 'exportCustomerData' && method_exists($processor, 'exportCustomerData')) {
            return $processor->exportCustomerData($identifier);
        }

        if ($methodName === 'exportGuestData' && method_exists($processor, 'exportGuestData')) {
            return $processor->exportGuestData($identifier);
        }

        return $processor->export($identifier); // Old method, will be removed in next releases
    }

    /**
     * Return current date.
     *
     * @return false|string
     */
    private function getDateStamp()
    {
        return date('Y-m-d_H-i-s', $this->dateTime->gmtTimestamp());
    }

    /**
     * Create .csv file.
     *
     * @param string     $fileName
     * @param array|null $data
     *
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function createFile(string $fileName, ?array $data): void
    {
        if (! $data) {
            return;
        }

        $this->csvWriter
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->saveData($fileName, $data);
    }

    /**
     * Delete .csv file.
     *
     * @param string $fileName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function deleteFile(string $fileName): void
    {
        if ($this->file->isExists($fileName)) {
            $this->file->deleteFile($fileName);
        }
    }

    /**
     * @inheritDoc
     */
    public function getFileName(string $dateTime): string
    {
        return "customer_data_$dateTime";
    }
}
