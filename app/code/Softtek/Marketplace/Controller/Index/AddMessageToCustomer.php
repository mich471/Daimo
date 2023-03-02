<?php
namespace Softtek\Marketplace\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Magento\Framework\Controller\Result\ForwardFactory;
use Purpletree\Marketplace\Model\SellerorderFactory;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder;
use Purpletree\Marketplace\Helper\Data;
use Magento\Sales\Api\OrderRepositoryInterface;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AddMessageToCustomer extends Action
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var CollectionFactory
     */
    protected $sellerOrderCollectionFactory;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param CustomerSession $customer
     * @param StoreManagerInterface $storeManager,
     * @param Seller $storeDetails,
     * @param ForwardFactory $resultForwardFactory,
     * @param Sellerorder $sellerorder,
     * @param Data $dataHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param CollectionFactory $sellerOrderCollectionFactory
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig,
     */
    public function __construct(
        Context $context,
        CustomerSession $customer,
        StoreManagerInterface $storeManager,
        Seller $storeDetails,
        ForwardFactory $resultForwardFactory,
        Sellerorder $sellerorder,
        Data $dataHelper,
        OrderRepositoryInterface $orderRepository,
        CollectionFactory $sellerOrderCollectionFactory,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->customer                      = $customer;
        $this->_sellerorder                 = $sellerorder;
        $this->storeManager                  = $storeManager;
        $this->dataHelper                    = $dataHelper;
        $this->storeDetails                  = $storeDetails;
        $this->resultForwardFactory          = $resultForwardFactory;
        $this->orderRepository = $orderRepository;
        $this->sellerOrderCollectionFactory = $sellerOrderCollectionFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $customerId = $this->customer->getCustomer()->getId();
        $sellerId = $this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        $id  = $this->getRequest()->getParam('order_id');
        if (!$id || $sellerId=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $sellerorderr = $this->existsSellerOrder($id);
        if (!$sellerorderr) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $historyData = $this->getRequest()->getPost('history');
        $notify = true;
        $visible = true;
        if (empty($historyData['comment'])) {
            $this->messageManager->addErrorMessage(__('Invalid message'));
            return $this->_redirect('marketplace/index/orderview/order_id/'.$id);
        }
        try {
            $order = $this->orderRepository->get($id);
            $sellerStatus = $order->getStatus();
            $history = $order->addStatusHistoryComment($historyData['comment'], $sellerStatus);
            $history->setIsVisibleOnFront($visible);
            $history->setIsCustomerNotified($notify);
            $history->setSmIsMessage(1);
            $history->setSmSellerMessage(1);
            $history->setSmCustomerMessage(0);
            $history->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return $this->_redirect('marketplace/index/orderview/order_id/'.$id);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We cannot add order message.'));
            return $this->_redirect('marketplace/index/orderview/order_id/'.$id);
        }
        $comment = trim(strip_tags($historyData['comment']));
        //Send notification to customer
        $this->sendCustomerNotification($order, $comment);

        $this->messageManager->addSuccess(__('Message sent successfully'));

        return $this->_redirect('marketplace/index/orderview/order_id/'.$id);
    }

    /**
     * Get Seller Order Collection
     *
     * @param integer $id
     * @return boolean
     */
    public function existsSellerOrder($id)
    {
        $collection = $this->sellerOrderCollectionFactory->create();
        $sellerId = $this->customer->getCustomer()->getId();
        foreach ($collection as $order) {
            if ($sellerId == $order->getSellerId()) {
                if ($id  == $order->getOrderId()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Send email notification to customer
     *
     * @param $order
     * @param $comment
     * @return void
     */
    protected function sendCustomerNotification($order, $comment)
    {
        try {
            $templateOptions = array('area' => Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
            $templateVars = array(
                'store' => $this->storeManager->getStore(),
                'store_name' => $this->storeManager->getStore()->getName(),
                'receiver_name' => $order->getCustomerFirstname(),
                'order_number' => $order->getIncrementId(),
                'comment' => $comment,
                'order_url' => $this->_url->getUrl('sales/order/view', ['order_id' => $order->getId()])
            );

            $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);
            $senderName = $this->scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE);
            $from = ['email' => $senderEmail, 'name' => $senderName];
            $this->inlineTranslation->suspend();
            $to = [$order->getCustomerEmail()];

            $templateId = $this->scopeConfig->getValue ( 'purpletree_marketplace/general/order_message_for_customer', ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId)->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

            $this->messageManager->addSuccessMessage(__('Message sent successfully'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error sending email notification'));
        }
    }
}
