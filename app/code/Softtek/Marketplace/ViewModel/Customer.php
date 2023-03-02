<?php

namespace Softtek\Marketplace\ViewModel;


use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Model\Session;
use Purpletree\Marketplace\Model\ResourceModel\Seller;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Request\Http;
use Magepow\CancelOrder\Model\Requests as CancelRequest;
use Softtek\Marketplace\Model\OrderReview ;
class Customer implements ArgumentInterface
{
    /**
     * @var OrderReview
     */
    protected $orderReview;
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Seller
     */
    protected $storeDetails;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var CancelRequest
     */
    protected $cancelRequest;

    /**
     * @param Session $customerSession
     * @param Seller $storeDetails
     * @param Order $order
     * @param Http $request
     * @param CancelRequest $cancelRequest
     * @param OrderReview $orderReview
     */
    public function __construct(
        Session       $customerSession,
        Seller        $storeDetails,
        Order         $order,
        Http          $request,
        CancelRequest $cancelRequest,
        OrderReview $orderReview
    )
    {
        $this->customerSession = $customerSession;
        $this->storeDetails = $storeDetails;
        $this->order = $order;
        $this->request = $request;
        $this->cancelRequest = $cancelRequest;
        $this->orderReview = $orderReview;
    }

    /**
     * Get Current Customer
     * @return \Magento\Customer\Model\Customer
     */
    public function getCurrentCustomer() {
        return $this->customerSession->getCustomer();
    }

    /**
     * @return Review Orde Id
     */
    public function hasOrderReview()
    {
        $orderReview = $this->orderReview->load($this->getOrder()->getId(), 'order_id');
        if (count($orderReview->getData()) > 0) {
            return true;
        }
        return false;
    }
    public function getStoreDetails($id)
    {
        return $this->storeDetails->getStoreDetails($id);
    }

    public function getId() {
        return $this->customerSession->getId();
    }

    /**
     * Get Order object
     * @return Order Object
     */
    public function getOrder()
    {
        $orderId = $this->request->getParam('order_id');
        if ($orderId) {
            return $this->order->load($orderId);
        }

        return null;
    }

    /**
     * Get Bank Ticket Number
     * @return Order Object
     */
    public function getBankTicketNumber()
    {
        try {
            $payment = $this->getOrder()->getPayment();
            $additionalInfoArray = $payment->getAdditionalInformation();
            if (isset($additionalInfoArray['boleto_boleto_number'])) {
                return $additionalInfoArray['boleto_boleto_number'];
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('Error getting up the additional information json value from order ID %1', $this->getOrder()->getId()));
        }

        return '';
    }

    /**
     * Get Bank Ticket Number
     * @return Order Object
     */
    public function getCancelRequestData()
    {
        $request = $this->cancelRequest->load($this->getOrder()->getId(), 'order_id');
        if ($request) {
            return $request->getData();
        }

        return [];
    }

    /**
     * Is payment captured
     * @return Order Object
     */
    public function isPaymentCaptured()
    {
        foreach ($this->getOrder()->getStatusHistories() as $history) {
            if ($history->getStatus() == 'processing') {
                return true;
                break;
            }
        }

        return false;
    }

    /**
     * Get Seller ID By Order ID
     * @return Order Object
     */
    public function getSellerIdByOrderId($orderId)
    {
        $adapter = $this->resourceConnection->getConnection();
        $select = $adapter->select()
            ->from($adapter->getTableName('purpletree_marketplace_sellerorder'), ['seller_id'])
            ->where('order_id = ?', $orderId)
            ->order("entity_id DESC");
        $sellerId = $adapter->fetchOne($select);

        if ($sellerId) {
            return $sellerId;
        }

        return false;
    }
}
