<?php

namespace Softtek\ReviewProductQuestion\Controller\Adminhtml\Question;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use PHPCuong\ProductQuestionAndAnswer\Model\QuestionFactory;
use PHPCuong\ProductQuestionAndAnswer\Model\ResourceModel\Question\CollectionFactory;

class MassStatus extends Action
{
    /**
     * @var string
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    protected $questionFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    protected $logger;

    /**
     * @param Context $context
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        QuestionFactory $questionFactory,
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->questionFactory = $questionFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            foreach ($collection as $item){
                $model = $this->questionFactory->create()->load($item['question_id']);
                $model->setData('question_status_id', $this->getRequest()->getParam('status'));
                $model->save();
            }

        } catch (\Exception $e) {
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
