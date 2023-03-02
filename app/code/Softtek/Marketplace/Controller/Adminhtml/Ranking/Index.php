<?php

namespace Softtek\Marketplace\Controller\Adminhtml\Ranking;

use \Magento\Backend\App\Action\Context as Context;
use \Magento\Framework\View\Result\PageFactory as PageFactory;
use \Magento\Backend\App\Action as Action;

class  Index extends Action
{
    protected $resultPageFactory = false;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Avaliações dos pedidos')));

        return $resultPage;
    }


}
