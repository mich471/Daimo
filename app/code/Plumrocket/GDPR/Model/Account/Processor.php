<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Account;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\DataPrivacyApi\Api\DataExportProcessorInterface;
use Plumrocket\DataPrivacyApi\Api\DataRemovalProcessorInterface;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Model\Archive\Zip;

/**
 * Export customer data.
 *
 * @deprecated since 3.1.0
 * @see \Plumrocket\DataPrivacy\Model\Account\Exporter
 * @see \Plumrocket\DataPrivacy\Model\Account\Remover
 */
class Processor extends AbstractModel implements DataRemovalProcessorInterface, DataExportProcessorInterface
{
    const INTEGRATED_PREFIX = 'prgdpr_';

    const CORE_PREFIX = 'Magento_';

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Csv
     */
    protected $csvWriter;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var array
     */
    protected $processors;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * @var Zip
     */
    protected $zip;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    private $customerDataFactory;

    /**
     * Array of \Magento\Customer\Api\Data\CustomerInterface with only emails
     *
     * @var CustomerInterface[]
     * @deprecated since 2.0.0
     */
    private $fakeCustomerDataModels = [];

    /**
     * @param \Magento\Framework\Model\Context                    $context
     * @param \Magento\Framework\Registry                         $registry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime         $dateTime
     * @param \Magento\Framework\App\Response\Http\FileFactory    $fileFactory
     * @param \Magento\Framework\Filesystem\Driver\File           $file
     * @param \Magento\Framework\File\Csv                         $csvWriter
     * @param \Plumrocket\GDPR\Model\Archive\Zip                  $zip
     * @param \Magento\Framework\Module\ModuleListInterface       $moduleList
     * @param \Magento\Framework\Filesystem\DirectoryList         $dir
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory
     * @param array                                               $processors
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        DateTime $dateTime,
        FileFactory $fileFactory,
        File $file,
        Csv $csvWriter,
        Zip $zip,
        ModuleListInterface $moduleList,
        DirectoryList $dir,
        CustomerInterfaceFactory $customerDataFactory,
        array $processors = []
    ) {
        parent::__construct($context, $registry);

        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
        $this->file = $file;
        $this->csvWriter = $csvWriter;
        $this->moduleList = $moduleList;
        $this->dir = $dir;
        $this->processors = $processors;
        $this->zip = $zip;
        $this->customerDataFactory = $customerDataFactory;
    }

    /**
     * @deprecated since 3.1.0
     * @see \Plumrocket\DataPrivacy\Model\Account\Exporter::exportCustomerData
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $this->export($customer, 'exportCustomerData');
        return null;
    }

    /**
     * @deprecated since 3.1.0
     * @see \Plumrocket\DataPrivacy\Model\Account\Exporter::exportGuestData
     */
    public function exportGuestData(string $email): ?array
    {
        $this->export($email, 'exportGuestData');
        return null;
    }

    /**
     * This function return .zip file with customer data.
     * @deprecated since 2.0.0
     * @see \Plumrocket\GDPR\Model\Account\Processor::exportCustomerData
     * @see \Plumrocket\GDPR\Model\Account\Processor::exportGuestData
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function exportData(CustomerInterface $customer)
    {
        $this->export($customer, 'export');
    }

    /**
     * This function return .zip file with customer data.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface|string $identifier
     * @param string                                              $methodName
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function export($identifier, string $methodName)
    {
        $dir = $this->dir->getPath('tmp');
        $date = $this->getDateStamp();
        $zipFileName = 'customer_data_' . $date . '.zip';
        $processors = $this->combineProcessors($this->processors);

        foreach ($processors as $processorData) {
            $processor = $processorData['processor'];
            $file = $processorData['file'];

            $dataExport = $this->runExportProcessor($processor, $identifier, $methodName);

            if (!is_array($file)) {
                $file = ['key1' => $file];
                $dataExport = ['key1' => $dataExport];
            }

            foreach ($file as $key => $name) {
                $fileName = $dir . DIRECTORY_SEPARATOR . $name . '_' . $date . '.csv';

                if (isset($dataExport[$key])) {
                    $this->createFile($fileName, $dataExport[$key]);
                    $this->zip->pack(
                        $fileName,
                        $dir . DIRECTORY_SEPARATOR . $zipFileName
                    );
                    $this->deleteFile($fileName);
                }
            }
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
     * Process data deletion or anonymization.
     *
     * @param CustomerInterface $customer
     * @return void
     * @see        \Plumrocket\GDPR\Model\Account\Processor::deleteGuestData
     *
     * @deprecated since 2.0.0
     * @see \Plumrocket\GDPR\Model\Account\Processor::deleteCustomerData
     */
    public function deleteData(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            $this->deleteCustomerData($customer);
        } else {
            $this->deleteGuestData($customer->getEmail());
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        $processors = $this->combineProcessors($this->processors);

        foreach ($processors as $processorData) {
            $processor = $processorData['processor'];
            if ($processor instanceof DataRemovalProcessorInterface
                || method_exists($processor, 'deleteCustomerData')
            ) {
                $processor->deleteCustomerData($customer);
            } else {
                $processor->delete($customer);
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteGuestData(string $email): bool
    {
        $processors = $this->combineProcessors($this->processors);

        foreach ($processors as $processorData) {
            $processor = $processorData['processor'];
            if ($processor instanceof DataRemovalProcessorInterface
                || method_exists($processor, 'deleteGuestData')
            ) {
                $processor->deleteGuestData($email);
            } else {
                $fakeCustomer = $this->createFakeCustomerDataModel($email);
                $processor->delete($fakeCustomer);
            }
        }

        return true;
    }

    /**
     * Create .csv file.
     *
     * @param string $fileName
     * @param array $data
     *
     * @return void
     */
    private function createFile($fileName, $data)
    {
        if (!$data) {
            return null;
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
    private function deleteFile($fileName)
    {
        if ($this->file->isExists($fileName)) {
            $this->file->deleteFile($fileName);
        }
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

    public function combineProcessors($data)
    {
        $processorsArray = ['external' => [], 'integrated' => [], 'core' => []];
        $processors = [];

        foreach ($data as $key => $value) {
            // check module exist and enabled
            $moduleName = str_replace(self::INTEGRATED_PREFIX, '', $key);
            $fileName = $key;

            if (is_array($value)) {
                if (!empty($value['module_name'])) {
                    $moduleName = $value['module_name'];
                }
                if (!empty($value['export_file_name'])) {
                    $fileName = $value['export_file_name'];
                }
            }

            if (! $this->moduleList->has($moduleName)) {
                continue;
            }

            $processor = $this->getProcessor($value);

            if (null !== $processor) {
                $processorData = ['file' => $fileName, 'processor' => $processor];

                if ($this->isIntegrated($key) && $this->checkVersion($moduleName, $processor)) {
                    $processorsArray['integrated'][$moduleName] = $processorData;
                } elseif ($this->isCore($moduleName)) {
                    $processorsArray['core'][$key] = $processorData;
                } else {
                    $processorsArray['external'][$moduleName] = $processorData;
                }

                $processorData = null;
            }
        }

        foreach ($processorsArray as $array) {
            foreach ($array as $name => $processorData) {
                if (! array_key_exists($name, $processors)) {
                    $processors[$name] = $processorData;
                }
            }
        }

        return $processors;
    }

    /**
     * @param $data
     * @return mixed|null
     */
    private function getProcessor($data)
    {
        if (is_object($data)) {
            return $data;
        }

        if (is_array($data) && ! empty($data['processor'])) {
            return $data['processor'];
        }

        return null;
    }

    /**
     * @param $moduleName
     * @param $processor
     * @return bool
     */
    public function checkVersion($moduleName, $processor)
    {
        if (method_exists($processor, 'getSupportedVersions')) {
            $module = $this->moduleList->getOne($moduleName);
            $moduleVersion = $module['setup_version'];
            $supportedVersions = $processor->getSupportedVersions();

            foreach ($supportedVersions as $value) {
                $dashPos = strpos($value, "-");

                if (false !== $dashPos && $dashPos > 0) {
                    $version = explode("-", $value);

                    if (version_compare($moduleVersion, $version[0], '>=')
                        && version_compare($moduleVersion, $version[1], '<=')
                    ) {
                        return true;
                    }
                } elseif (version_compare($moduleVersion, $value, '=')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isIntegrated($key)
    {
        return strpos($key, self::INTEGRATED_PREFIX) === 0;
    }

    /**
     * @param $moduleName
     * @return bool
     */
    public function isCore($moduleName)
    {
        return strpos($moduleName, self::CORE_PREFIX) === 0;
    }

    /**
     * @param        $processor
     * @param        $identifier
     * @param string $methodName
     * @return array|null
     * @throws \Exception
     */
    private function runExportProcessor($processor, $identifier, string $methodName)
    {
        if ($methodName === 'exportCustomerData' &&
            ($processor instanceof DataExportProcessorInterface || method_exists($processor, 'exportCustomerData'))
        ) {
            return $processor->exportCustomerData($identifier);
        }

        if ($methodName === 'exportGuestData' &&
            ($processor instanceof DataExportProcessorInterface || method_exists($processor, 'exportGuestData'))
        ) {
            return $processor->exportGuestData($identifier);
        }

        if ($processor instanceof DataProcessorInterface || method_exists($processor, 'export')) {
            if (! $identifier instanceof CustomerInterface) {
                $identifier = $this->createFakeCustomerDataModel($identifier);
            }
            return $processor->export($identifier);
        }

        return null;
    }

    /**
     * @deprecated since 2.0.0
     * @param string $email
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function createFakeCustomerDataModel(string $email): CustomerInterface
    {
        if (! isset($this->fakeCustomerDataModels[$email])) {
            $fakeCustomer = $this->customerDataFactory->create();
            $fakeCustomer->setEmail($email);

            $this->fakeCustomerDataModels[$email] = $fakeCustomer;
        }

        return $this->fakeCustomerDataModels[$email];
    }

    /**
     * @return array
     */
    public function getAllProcessors(): array
    {
        return $this->combineProcessors($this->processors);
    }

    public function getFileName(string $dateTime): string
    {
        return "customer_data_$dateTime";
    }
}
