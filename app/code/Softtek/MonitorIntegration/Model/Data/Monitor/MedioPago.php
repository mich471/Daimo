<?php


namespace Softtek\MonitorIntegration\Model\Data\Monitor;


use Softtek\MonitorIntegration\Api\Data\Monitor\MedioPagoInterface;

class MedioPago extends \Magento\Framework\Api\AbstractExtensibleObject implements MedioPagoInterface
{
    public function getFormaPago()
    {
        return $this->_get(SELF::FORMA_PAGO);
    }

    public function setFormaPago($formaPago)
    {
        $this->setData(SELF::FORMA_PAGO, $formaPago);
    }

    public function getMonto()
    {
        return $this->_get(SELF::MONTO);
    }

    public function setMonto($monto)
    {
        $this->setData(SELF::MONTO, $monto);
    }

    public function getCodigoAutorizacion()
    {
        return $this->_get(SELF::CODIGO_AUTORIZACION);
    }

    public function setCodigoAutorizacion($codigoAutorizacion)
    {
        $this->setData(SELF::CODIGO_AUTORIZACION, $codigoAutorizacion);
    }

    public function getCodigoTbk()
    {
        return $this->_get(SELF::CODIGO_TBK);
    }

    public function setCodigoTbk($codigoTbk)
    {
        $this->setData(SELF::CODIGO_TBK, $codigoTbk);
    }

    public function getObjetoTrx()
    {
        return $this->_get(SELF::OBJETO_TRX);
    }

    public function setObjetoTrx($objetoTrx)
    {
        $this->setData(SELF::OBJETO_TRX, $objetoTrx);
    }

    public function getTipoPago()
    {
        return $this->_get(SELF::TIPO_PAGO);
    }

    public function setTipoPago($tipoPago) {
        $this->setData(SELF::TIPO_PAGO, $tipoPago);
    }
}
