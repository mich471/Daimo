<?php


namespace Softtek\ReverseTransactions\Model;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Softtek\ReverseTransactions\Helper\ConfigHelper;
use Psr\Log\LoggerInterface;

class LoggerCsv
{

    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var Csv
     */
    private $csvProcessor;
    /**
     * @var File
     */
    private $fileDriver;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var TimezoneInterface
     */
    private $dateTime;

    /**
     * LoggerCsv constructor.
     * @param Csv $csvProcessor
     * @param DirectoryList $directoryList
     * @param Filesystem $filesystem
     * @param File $fileDriver
     * @param TimezoneInterface $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        Csv $csvProcessor,
        DirectoryList $directoryList,
        Filesystem $filesystem,
        File $fileDriver,
        TimezoneInterface $dateTime,
        LoggerInterface $logger
    )
    {
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->csvProcessor = $csvProcessor;
        $this->fileDriver = $fileDriver;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Create logger for reverse transactions attempts by day,
     * it appends information to the daily file every hour
     * @param $newData
     * @param $type
     * @return bool
     */
    function writeToCsv($newData, $type)
    {
        try {

            $fileDirectoryPath = $this->directoryList->getPath(DirectoryList::VAR_DIR) . "/reverse/" . $type;
            $fileName = $type . "_" . $this->dateTime->date()->format('d_m_Y');
            if (!is_dir($fileDirectoryPath))
                mkdir($fileDirectoryPath, 0777, true);

            $filePath = $fileDirectoryPath . '/' . $fileName . '.csv';

            if (isset($newData) && count($newData) > 0) {

                $content = [];
                $header = [];
                $keys = array_keys($newData[0]->getData());

                foreach ($keys as $key) {
                    $header[] = $key;
                }

                if ($this->fileDriver->isExists($filePath)) {
                    $data = $this->csvProcessor->getData($filePath);
                    $content = $data;
                } else {
                    $content[] = $header;
                }

                return $this->writeFile($newData, $keys, $filePath, $content);
            }

        } catch (FileSystemException | \Exception $e) {
            $this->logger->error(
                "Error when writing daily reverse transactions log " .
                $type . " ".
                $e->getMessage());
            return false;
        }


    }

    /**
     * Create Csv file to be sent to client every hour
     * column headers needs a format, different from the format in the file
     * Create a new file every N hour, based on cron schedule
     * @param $data
     * @param $type
     * @return string
     */
    function writeEmailCsv($data, $type)
    {
        try {
            $fileDirectoryPath = $this->directoryList->getPath(DirectoryList::VAR_DIR) . "/reverse/" . $type . "/emails";
            $fileName = $type . "_" . $this->dateTime->date()->format('d_m_Y_H_i');
            if (!is_dir($fileDirectoryPath))
                mkdir($fileDirectoryPath, 0777, true);

            $filePath = $fileDirectoryPath . '/' . $fileName . '.csv';

            if (isset($data) && count($data) > 0) {

                $content = [];
                $header = [];
                $keys = ConfigHelper::KEYS_CLIENT_FILE;
                foreach ($keys as $key) {
                    $header[] = str_replace("_", " ", $key);
                }

                $content[] = $header;
                if ($this->writeFile($data, $keys, $filePath, $content)) {
                    return $filePath;
                }

            } else {
                return "";
            }
            return $filePath;
        } catch (FileSystemException | \Exception $e) {
            $this->logger->error(
                "Error when writing email CSV reverse transactions file " .
                $type . " " .
                $e->getMessage());
            return "";
        }
    }

    /**
     * Write CSV file to disk
     * @param $data
     * @param $keys
     * @param $filePath
     * @param $content
     */
    private function writeFile($data, $keys, $filePath, $content){

        foreach ($data as $row) {
            $csvRow = [];
            foreach ($keys as $key) {
                $csvRow[] = $row->getData($key);
            }
            $content[] = $csvRow;
        }

        try {
            $this->csvProcessor
                ->setEnclosure('"')
                ->setDelimiter(',')->appendData($filePath, $content);
            return true;
        } catch (FileSystemException $e) {
            $this->logger->error("Error when writing CSV file to disk " . $filePath .
                " " . $e->getMessage());
            return false;
        }
    }
}
