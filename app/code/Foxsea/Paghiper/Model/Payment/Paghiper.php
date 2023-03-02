<?php
namespace Foxsea\Paghiper\Model\Payment;

use Magento\Framework\App\RequestInterface;

class Paghiper extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canOrder                = true;
    protected $_code                    = 'foxsea_paghiper';
    protected $_infoBlockType = 'Foxsea\Paghiper\Block\Payment\Info\Paghiper';
    protected $_supportedCurrencyCodes  = ['BRL'];

    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount) {
        if ($this->canOrder()) {
            $info = $this->getInfoInstance();

            $order = $payment->getOrder();
            $data = $this->helper()->createOrderArray($order, $payment);

            if (!isset($data['error'])) {
                $generate = $this->helper()->generate($data);
                if(isset($generate['success']) && $generate['success']){
                    $this->helper()->addInformation($order, $generate['additional']);
                }
            } else {
                $message = isset($data['error_message']) ? $data['error_message'] : 'Erro ao gerar boleto.';
                throw new \Magento\Framework\Exception\CouldNotSaveException(
                    __($message)
                );
            }
        }
    }

    protected function helper() {
        return \Magento\Framework\App\ObjectManager::getInstance()->get('Foxsea\Paghiper\Helper\Order');
    }
}

