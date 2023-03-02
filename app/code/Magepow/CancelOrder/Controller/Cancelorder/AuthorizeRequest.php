<?php
namespace Magepow\CancelOrder\Controller\Cancelorder;

use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Magepow\CancelOrder\Model\RequestsFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magepow\CancelOrder\Model\ResourceModel\Requests as ResourceModelRequests;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Purpletree\Marketplace\Model\SellerorderFactory;
use Purpletree\Marketplace\Model\ResourceModel\Sellerorder;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;
use Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;

class AuthorizeRequest extends Action
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
     * @var ResourceModelRequests
     */
    private $_resourceRequests;

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
     * @var Invoice
     */
    protected $_invoice;

    /**
     * @var CreditmemoService
     */
    protected $_creditmemoService;

    /**
     * @var CreditmemoFactory
     */
    protected $_creditmemoFactory;

    /**
     * @var OrderCommentSender
     */
    protected $_orderCommentSender;

    /**
     * @var CreditmemoSender
     */
    protected $_creditmemoSender;

    /**
     * @var CreditmemoLoader
     */
    protected $_creditmemoLoader;

    /**
     * @var CreditmemoManagementInterface
     */
    protected $_creditmemoManagement;

    /**
     * @var ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var OrderStatusHistoryRepositoryInterface
     */
    protected $_orderStatusRepository;

    /**
     * @param Context $context
     * @param RequestsFactory $requestsFactory
     * @param JsonFactory $resultJsonFactory
     * @param ResourceModelRequests $resourceRequests
     * @param OrderRepositoryInterface $orderRepository
     * @param SellerorderFactory $sellerorderFactory
     * @param Sellerorder $sellerorder
     * @param Invoice $invoice
     * @param CreditmemoService $creditmemoService
     * @param CreditmemoFactory $creditmemoFactory
     * @param OrderCommentSender $orderCommentSender
     * @param CreditmemoSender $creditmemoSender
     * @param CreditmemoLoader $creditmemoLoader
     * @param CreditmemoManagementInterface $creditmemoManagement
     * @param ResourceConnection $resourceConnection
     * @param OrderStatusHistoryRepositoryInterface $orderStatusRepository
     */
    public function __construct(
        Context $context,
        RequestsFactory $requestsFactory,
        JsonFactory $resultJsonFactory,
        ResourceModelRequests $resourceRequests,
        OrderRepositoryInterface $orderRepository,
        SellerorderFactory $sellerorderFactory,
        Sellerorder $sellerorder,
        Invoice $invoice,
        CreditmemoService $creditmemoService,
        CreditmemoFactory $creditmemoFactory,
        OrderCommentSender $orderCommentSender,
        CreditmemoSender $creditmemoSender,
        CreditmemoLoader $creditmemoLoader,
        CreditmemoManagementInterface $creditmemoManagement,
        ResourceConnection $resourceConnection,
        OrderStatusHistoryRepositoryInterface $orderStatusRepository
    )
    {
        $this->_requestsFactory = $requestsFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_resourceRequests = $resourceRequests;
        $this->_orderRepository = $orderRepository;
        $this->_sellerorderFactory = $sellerorderFactory;
        $this->_sellerorder = $sellerorder;
        $this->_invoice = $invoice;
        $this->_creditmemoService = $creditmemoService;
        $this->_creditmemoFactory = $creditmemoFactory;
        $this->_orderCommentSender = $orderCommentSender;
        $this->_creditmemoSender = $creditmemoSender;
        $this->_creditmemoLoader = $creditmemoLoader;
        $this->_creditmemoManagement = $creditmemoManagement;
        $this->_resourceConnection = $resourceConnection;
        $this->_orderStatusRepository = $orderStatusRepository;

        return parent::__construct($context);
    }
    /**
     * View page action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            $data = (array)$this->getRequest()->getPost();
            $errMsg = __("We can not authorize the request, Please try again.");
            $result = $this->_resultJsonFactory->create();
            $resultArray = ['success' => false, 'message' => $errMsg];
            if (!isset($data['ac_order_id'])) {
                return $result->setData($resultArray);
            }
            $data['status'] = 1;
            $model = $this->_requestsFactory->create();
            $model->load($data['ac_order_id'],'order_id');
            if (!$model->getId()) {
                return $result->setData($resultArray);
            }
            foreach ($data as $k => $v) {
                $model->setData($k, $v);
            }
            $this->_resourceRequests->save($model);

            $order = $this->_orderRepository->get($data['ac_order_id']);
            if (!$order->getId()) {
                return $result->setData($resultArray);
            }

            $statusForReturn = ['entregue', 'entrega_confirmada'];

            $payment = $order->getPayment();
            $method = $payment->getMethodInstance();
            $methodCode = $method->getCode();

            $previousStatus = $this->getPreviousOrderStatus($order);
            $newState = "";
            $newStatus = "";
            if ($methodCode == 'foxsea_paghiper') {
                if (in_array($previousStatus, $statusForReturn)) {
                    $newState = "canceled";
                    $newStatus = "devolvidobb";
                } else {
                    $newState = "canceled";
                    $newStatus = "canceled";
                }
            } else {
                if (in_array($previousStatus, $statusForReturn)) {
                    $newState = "closed";
                    $newStatus = "devolvidoc";
                } else {
                    $newState = "closed";
                    $newStatus = "closed";
                }
            }

            if ($order->canCancel()) {
                $order->cancel()->save();
            } else {
                $creditMemoData = [];
                $creditMemoData['do_offline'] = 1;
                $creditMemoData['shipping_amount'] = 0;
                $creditMemoData['adjustment_positive'] = 0;
                $creditMemoData['adjustment_negative'] = 0;
                $creditMemoData['comment_text'] = __('Credit memo generated in the cancellation request authorization');
                $creditMemoData['send_email'] = 1;
                foreach ($order->getAllVisibleItems() as $_item) {
                    $itemToCredit[$_item->getId()] = ['qty' => $_item->getQtyOrdered()];
                }
                $creditMemoData['items'] = $itemToCredit;
                try {
                    $this->_creditmemoLoader->setOrderId($order->getId()); //pass order id
                    $this->_creditmemoLoader->setCreditmemo($creditMemoData);
                    $creditmemo = $this->_creditmemoLoader->load();
                    if ($creditmemo) {
                        if (!$creditmemo->isValidGrandTotal()) {
                            throw new LocalizedException(
                                __('The credit memo\'s total must be positive.')
                            );
                        }
                        if (!empty($creditMemoData['comment_text'])) {
                            $creditmemo->addComment(
                                $creditMemoData['comment_text'],
                                true,
                                true
                            );
                            $creditmemo->setCustomerNote($creditMemoData['comment_text']);
                            $creditmemo->setCustomerNoteNotify(true);
                        }
                        //$creditmemoManagement = $this->_creditmemoManagement->create();
                        $creditmemo->getOrder()->setCustomerNoteNotify(!empty($creditMemoData['send_email']));
                        $this->_creditmemoManagement->refund($creditmemo, (bool)$creditMemoData['do_offline']);
                        if (!empty($creditMemoData['send_email'])) {
                            $this->_creditmemoSender->send($creditmemo);
                        }

                        //Removing first unnecessary complete status
                        $order = $this->_orderRepository->get($data['ac_order_id']);
                        foreach ($order->getStatusHistories() as $history) {
                            if ($history->getStatus() == 'complete') {
                                $orderStatusCommentObject = $this->_orderStatusRepository->get($history->getId());
                                $this->_orderStatusRepository->delete($orderStatusCommentObject);
                            }
                        }
                    }
                } catch (LocalizedException $e) {
                    return $result->setData($resultArray);
                } catch (\Exception $e) {
                    return $result->setData($resultArray);
                }
            }
            $connection = $this->_resourceConnection->getConnection();
            $ordersTableName = $connection->getTableName('sales_order');
            $ordersGridTableName = $connection->getTableName('sales_order_grid');
            $orderId = (int)$order->getId();
            $forceUpdateOrderQuery = "UPDATE {$ordersTableName} SET state = '{$newState}', status = '{$newStatus}' WHERE entity_id = {$orderId}";
            $connection->query($forceUpdateOrderQuery); //This is to avoid order number change when a credit memo is generated.
            $forceUpdateOrderQuery = "UPDATE {$ordersGridTableName} SET status = '{$newStatus}' WHERE entity_id = {$orderId}";
            $connection->query($forceUpdateOrderQuery); //This is to avoid order number change when a credit memo is generated.
            //$order->setState($newState)->setStatus($newStatus)->save();

            $order = $this->_orderRepository->get($data['ac_order_id']);
            $entity_ids  = $this->_sellerorder->getEntityIdfromOrderId2($data['ac_order_id']);
            foreach ($entity_ids as $idd) {
                $sellerorder = $this->_sellerorderFactory->create();
                $sellerorder->load($idd['entity_id']);
                $sellerorder->setOrderStatus($newStatus);
                $sellerorder->save();
            }

            $history = $order->addStatusHistoryComment($data['seller_comment'], $newStatus);
            $history->setIsVisibleOnFront(true);
            $history->setIsCustomerNotified(true);
            $history->save();

            $resultArray['success'] = true;
            $resultArray['message'] = __('Request authorized successfully!');
        } catch (\Exception $e) {
            $resultArray['message'] = __($e->getMessage());
        }

        return $result->setData($resultArray);
    }

    /**
     * Get Previous Order Status
     * @return Order Object
     */
    public function getPreviousOrderStatus($order)
    {
        $previousStatus = "complete";
        foreach ($order->getStatusHistories() as $history) {
            if ($previousStatus == 'cancelamento_solicitado') {
                $previousStatus = $history->getStatus();
                break;
            }
            $previousStatus = $history->getStatus();
        }

        return $previousStatus;
    }
}
