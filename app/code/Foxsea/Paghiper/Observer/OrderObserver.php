<?php
namespace Foxsea\Paghiper\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;

class OrderObserver implements ObserverInterface
{
    /** @var OrderStatusHistoryRepositoryInterface */
    protected $orderStatusRepository;

    /**
     * OrderObserver constructor
     *
     * @param OrderStatusHistoryRepositoryInterface $orderStatusRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        OrderStatusHistoryRepositoryInterface $orderStatusRepository
    ) {
        $this->orderStatusRepository = $orderStatusRepository;
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        $methodCode = $method->getCode();
        if ($methodCode != 'foxsea_paghiper') {
            return $this;
        }
        $orderStatus = $this->helper()->getConfig('order_status');
        $status = ($orderStatus != '') ? $orderStatus : 'pending_payment';
        $order->addCommentToStatusHistory('Aguardando pagamento do boleto.', $status, true);
        $order->save();

        //Removing first unnecessary processing status
        foreach ($order->getStatusHistories() as $history) {
            if ($history->getStatus() == 'processing') {
                $orderStatusCommentObject = $this->orderStatusRepository->get($history->getId());
                $this->orderStatusRepository->delete($orderStatusCommentObject);
            }
        }
    }

    protected function helper(){
        return \Magento\Framework\App\ObjectManager::getInstance()->get('Foxsea\Paghiper\Helper\Data');
    }

    private function log($msg){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paghiper.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($msg);
    }

}
