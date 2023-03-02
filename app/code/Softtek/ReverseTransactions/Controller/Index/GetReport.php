<?php

/**
 * Do not commit this changes, just for local testing
 *
 * @package
 * @author
 * @copyright Softtek 2020
 */

namespace Softtek\ReverseTransactions\Controller\Index;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Backend\Model\Auth\Session;


class GetReport extends Action
{

    /**
     * @var ResultFactory
     */
    protected $resultFactory;
    /**
     * @var FileFactory
     */
    private $fileFactory;
    /**
     * @var Http
     */
    private $http;
    /**
     * @var ForwardFactory
     */
    private $forwardFactory;
    /**
     * @var Session
     */
    private $authSession;

    /**
     * Index Controller Constructor
     *
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param FileFactory $fileFactory
     * @param Http $http
     * @param ForwardFactory $forwardFactory
     * @param Session $authSession
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        FileFactory $fileFactory,
        Http $http,
        ForwardFactory $forwardFactory,
        Session  $authSession
    )
    {

        $this->resultFactory = $resultFactory;
        $this->fileFactory = $fileFactory;
        $this->http = $http;
        $this->forwardFactory = $forwardFactory;
        $this->authSession =  $authSession;
        parent::__construct($context);
    }

    public function execute()
    {

        $fileName = $this->http->getParam('rvtx');

        if ($fileName) {

            $type = explode("_", $fileName)[0];
            $filepath = "reverse/softtek_" . $type . "/emails/softtek_" . $fileName;
            $downloadedFileName = $fileName . ".csv";
            $content['type'] = 'filename';
            $content['value'] = $filepath;

            return $this->fileFactory->create($downloadedFileName, $content, \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        } else {
            $resultForward = $this->forwardFactory->create();
            $resultForward->setController('index');
            $resultForward->forward('defaultNoRoute');
            return $resultForward;
        }

    }
}
