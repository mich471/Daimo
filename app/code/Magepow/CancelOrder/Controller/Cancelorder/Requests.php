<?php

namespace Magepow\CancelOrder\Controller\Cancelorder;

use Magento\Framework\App\Action\Action;
use Magepow\CancelOrder\Model\RequestsFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Purpletree\Marketplace\Model\SellerorderFactory;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Requests extends Action
{
    /**
     * @var RequestsFactory
     */
    protected $_requestsFactory;

    /**
     * @var JsonFactory
     */
    private $_resultJsonFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var SellerorderFactory
     */
    protected $_sellerorderFactory;

    /**
     * @var Sellerorder
     */
    protected $_sellerorder;

    /**
     * @var OrderCommentSender
     */
    protected $_orderCommentSender;

    /**
     * @var CollectionFactory
     */
    protected $_sellerorderCollectionFactory;

    /**
     * @var StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Context $context
     * @param RequestsFactory $requestsFactory
     * @param JsonFactory $resultJsonFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param SellerorderFactory $sellerorderFactory
     * @param Sellerorder $sellerorder
     * @param OrderCommentSender $orderCommentSender
     * @param CollectionFactory $sellerorderCollectionFactory
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context                  $context,
        RequestsFactory          $requestsFactory,
        JsonFactory              $resultJsonFactory,
        OrderRepositoryInterface $orderRepository,
        SellerorderFactory       $sellerorderFactory,
        Sellerorder              $sellerorder,
        OrderCommentSender       $orderCommentSender,
        CollectionFactory        $sellerorderCollectionFactory,
        StateInterface           $inlineTranslation,
        TransportBuilder         $transportBuilder,
        ScopeConfigInterface     $scopeConfig,
        StoreManagerInterface    $storeManager
    )
    {
        $this->_requestsFactory = $requestsFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_orderRepository = $orderRepository;
        $this->_sellerorderFactory = $sellerorderFactory;
        $this->_sellerorder = $sellerorder;
        $this->_orderCommentSender = $orderCommentSender;
        $this->_sellerOrderCollectionFactory = $sellerorderCollectionFactory;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;

        return parent::__construct($context);
    }

    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $data = (array)$this->getRequest()->getPost();

            $errMsg = __("We can not sent the request, Please try again.");
            $result = $this->_resultJsonFactory->create();
            $resultArray = ['success' => false, 'message' => $errMsg];
            if (!isset($data['order_id'])) {
                return $result->setData($resultArray);
            }
            $data['status'] = 0;
            $model = $this->_requestsFactory->create();
            $model->load($data['order_id'], 'order_id');
            if ($model->getId()) {
                $resultArray['message'] = __('Cancellation or refund request already exists');
                return $result->setData($resultArray);
            }
            $model = $this->_requestsFactory->create();
            $model->setData($data)->save();

            $order = $this->_orderRepository->get($data['order_id']);
            $order->setStatus('cancelamento_solicitado')->setState('processing');
            $this->_orderRepository->save($order);
            $entity_ids = $this->_sellerorder->getEntityIdfromOrderId2($data['order_id']);
            foreach ($entity_ids as $idd) {
                $sellerorder = $this->_sellerorderFactory->create();
                $sellerorder->load($idd['entity_id']);
                $sellerorder->setOrderStatus('cancelamento_solicitado');
                $sellerorder->save();
            }

            if ($data['comment_reason'] == '' && $data['comment_reason_1'] != '') {
                $data['comment_reason'] = $data['comment_reason_1'];
            }
            if ($data['comment_reason'] == '') {
                $data['comment_reason'] = $data['reason'];
            }

            $history = $order->addStatusHistoryComment($data['comment_reason'], $order->getStatus());
            $history->setIsVisibleOnFront(true);
            $history->setIsCustomerNotified(true);
            $history->save();

            $comment = trim(strip_tags($data['comment_reason']));
            /** @var OrderCommentSender $orderCommentSender */
            $this->_orderCommentSender->send($order, true, $comment);

            $payment = $order->getPayment()->getMethodInstance();
	    $commentDetails = "";
            if ($orderCancelDevStatus == "cancelamento_solicitado") {
                $commentDetails = __('Cancellation reason') . ': ' . $data['reason'];
            } else {
                $commentDetails = __('Return reason') . ': ' . $data['reason'];
            }
            if ($data['comment_reason'] != '' && $data['comment_reason'] != $data['reason']) {
                $commentDetails .= ", " . __('Reason description') . ': ' . $data['comment_reason'];
            }

            $this->sendSellerNotification($order, $commentDetails, $data);

            $resultArray['success'] = true;
            $resultArray['message'] = __('Request sent successfully!');
        } catch (\Exception $e) {
            $resultArray['message'] = __($e->getMessage());
        }

        return $result->setData($resultArray);
    }

    /**
     * Send email notification to customer
     *
     * @param $order
     * @param $comment
     * @param $databank
     * @return void
     */
    protected function sendSellerNotification($order, $comment, $data)
    {
        try {
            $templateOptions = array('area' => Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
            $templateVars = [
                'store' => $this->_storeManager->getStore(),
                'store_name' => $this->_storeManager->getStore()->getName(),
                'receiver_name' => $order->getCustomerFirstname(),
                'order_number' => $order->getIncrementId(),
                'comment' => $comment,
                'payment_method' => $data["payment_method"],
                'order_url' => $this->_url->getUrl('sales/order/view', ['order_id' => $order->getId()])
            ];

            if (strtolower($data["payment_method"]) == strtolower("Boleto Bancário")) {
                $templateVars = array_merge($templateVars, [
                    'databank_cnpj' => $data["databank_cnpj"],
                    'databank_banknumber' => $data["databank_banknumber"],
                    'databank_actype' => $data["databank_actype"],
                    'databank_agnumber' => $data["databank_agnumber"],
                    'databank_acnumber' => $data["databank_acnumber"],
                ]);
            }

            $senderEmail = $this->_scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
            $senderName = $this->_scopeConfig->getValue('trans_email/ident_general/name', ScopeInterface::SCOPE_STORE);
            $from = ['email' => $senderEmail, 'name' => $senderName];
            $this->_inlineTranslation->suspend();

            $sellerEmail = "";
            $orderCollection = $this->_sellerOrderCollectionFactory->create();
            $orderCollection->addFieldToSelect('entity_id')
                ->join(
                    ['customer' => $orderCollection->getConnection()->getTableName('customer_entity')],
                    'main_table.seller_id = customer.entity_id',
                    ['email']
                )
                ->join(
                    ['seller' => $orderCollection->getConnection()->getTableName('purpletree_marketplace_stores')],
                    'main_table.seller_id = seller.seller_id',
                    ['store_name']
                )
                ->addFieldToFilter('main_table.order_id', $order->getId())
                ->addOrder('main_table.created_at', "desc");
            $orderCollection->load();
$a = $orderCollection->getSelect()->__toString();
file_put_contents("/var/www/Dow/var/log/aa_jesus.log", $a . "\n", FILE_APPEND);
            $to = [];
            if (count($orderCollection)) {
                $firstRecord = $orderCollection->getFirstItem();
                $sellerEmail = $firstRecord->getEmail();
                $to = [$sellerEmail];
                $templateVars['receiver_name'] = $firstRecord->getStoreName();
            }

            if (!count($to)) {
                $this->messageManager->addErrorMessage(__('Error sending email notification'));
                return false;
            }

            $templateId = $this->_scopeConfig->getValue('purpletree_marketplace/general/cancel_request_for_seller', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();

            $this->messageManager->addSuccessMessage(__('Message sent successfully'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error sending email notification'));
        }
    }
}
