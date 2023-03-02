<?php
/**
 * Softtek_Marketplace SellerData
 *
 * @category    Softtek
 * @package     Softtek_Marketplace
 * @author      J. Abraham Serena <jorge.serena@softtek.com>
 * @copyright   © Softtek 2022. All rights reserved.
 */
namespace Softtek\Questions\Controller\Question;

class Save extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJson;

    /**
     * @var \Softtek\Marketplace\Helper\Data
     */
    protected $dataHelper;

    /**
     * Save Constructor
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJson
     * @param \Softtek\Marketplace\Helper\Data $marketplaceHelper
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\Message\ManagerInterface $messageManager,
       \Magento\Framework\Controller\Result\JsonFactory $resultJson,
       \Softtek\Questions\Helper\Data $dataHelper
    )
    {
        $this->_messageManager      =   $messageManager;
        $this->_resultJson          =   $resultJson;
        $this->dataHelper           =   $dataHelper;

        return parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(\Magento\Framework\App\RequestInterface $request): ?\Magento\Framework\App\Request\InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPost();
        $response = [];

        try {
            if($postData->answer != ""){
                if($postData->idanswer != ""){
                    $this->dataHelper->updateQuestions($postData->answer, $postData->idanswer);
                }else{
                    $this->dataHelper->saveQuestions($postData->answer, $postData->nameseller, $postData->emailseller, $postData->idQuestion, $postData->userId);
                }
            }

        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'value' => __($e->getMessage())
            ];
        }
        $result = $this->_resultJson->create();

        return $result->setData($response);
    }
}
