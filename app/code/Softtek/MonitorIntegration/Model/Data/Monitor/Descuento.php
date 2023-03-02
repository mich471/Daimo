<?php


namespace Softtek\MonitorIntegration\Model\Data\Monitor;


class Descuento extends \Magento\Framework\Api\AbstractExtensibleObject implements \Softtek\MonitorIntegration\Api\Data\Monitor\DescuentosInterface
{
    public function getTipo() { $this->_get(SELF::TIPO); }
    public function getValorDescuento() { $this->_get(SELF::VALOR_DESCUENTO); }
    public function getCodigoDescuento() { $this->_get(SELF::CODIGO_DESCUENTO); }
    public function getDescripcionDescuento() { $this->_get(SELF::DESCRIPCION_DESCUENTO); }
    public function getAplicar() { $this->_get(SELF::APLICAR); }

    public function setTipo($tipo) { $this->setData(SELF::TIPO, $tipo); }
    public function setValorDescuento($valorDescuento) { $this->setData(SELF::VALOR_DESCUENTO, $valorDescuento); }
    public function setCodigoDescuento($codigoDescuento) { $this->setData(SELF::CODIGO_DESCUENTO, $codigoDescuento); }
    public function setDescripcionDescuento($descripcionDescuento) { $this->setData(SELF::DESCRIPCION_DESCUENTO, $descripcionDescuento); }
    public function setAplicar($aplicar) { $this->setData(SELF::APLICAR, $aplicar); }
}
