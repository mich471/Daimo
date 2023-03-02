<?php


namespace Softtek\MonitorIntegration\Model\Data\Monitor;


class Item extends \Magento\Framework\Api\AbstractExtensibleObject implements \Softtek\MonitorIntegration\Api\Data\Monitor\ItemInterface
{
    public function getEan1() { $this->_get(SELF::EAN1); }
    public function getEan2() { $this->_get(SELF::EAN2); }
    public function getEan3() { $this->_get(SELF::EAN3); }
    public function getEan4() { $this->_get(SELF::EAN4); }
    public function getEan5() { $this->_get(SELF::EAN5); }
    public function getFkDepartment() { $this->_get(SELF::FK_DEPARTMENT); }
    public function getMaterialSku() { $this->_get(SELF::MATERIAL_SKU); }
    public function getMaterialName() { $this->_get(SELF::MATERIAL_NAME); }
    public function getImageUrl() { $this->_get(SELF::IMAGE_URL); }
    public function getOrderQuantity() { $this->_get(SELF::ORDER_QUANTITY); }
    public function getFkPlant() { $this->_get(SELF::FK_PLANT); }
    public function getFkStatus() { $this->_get(SELF::FK_STATUS); }
    public function getStockAvailability() { $this->_get(SELF::STOCK_AVAILABILITY); }
    public function getPrecioUnitario() { $this->_get(SELF::PRECIO_UNITARIO); }
    public function getDescuentoTotal() { $this->_get(SELF::DESCUENTO_TOTAL); }
    public function getIva() { $this->_get(SELF::IVA); }
    public function getTipoFelicitacion() { $this->_get(SELF::TIPO_FELICITACION); }
    public function getMensajeFelicitacion() { $this->_get(SELF::MENSAJE_FELICITACION); }
    public function getDescuentos() {$this->_get(SELF::DESCUENTOS); }

    public function setEan1($ean1) { $this->setData(SELF::EAN1, $ean1); }
    public function setEan2($ean2) { $this->setData(SELF::EAN2, $ean2); }
    public function setEan3($ean3) { $this->setData(SELF::EAN3, $ean3); }
    public function setEan4($ean4) { $this->setData(SELF::EAN4, $ean4); }
    public function setEan5($ean5) { $this->setData(SELF::EAN5, $ean5); }
    public function setFkDepartment($fkDepartment) { $this->setData(SELF::FK_DEPARTMENT, $fkDepartment); }
    public function setMaterialSku($materialSku) { $this->setData(SELF::MATERIAL_SKU, $materialSku); }
    public function setMaterialName($materialName) { $this->setData(SELF::MATERIAL_NAME, $materialName); }
    public function setImageUrl($imageUrl) { $this->setData(SELF::IMAGE_URL, $imageUrl); }
    public function setOrderQuantity($orderQuantity) { $this->setData(SELF::ORDER_QUANTITY, $orderQuantity); }
    public function setFkPlant($fkPlant) { $this->setData(SELF::FK_PLANT, $fkPlant); }
    public function setFkStatus($fkStatus) { $this->setData(SELF::FK_STATUS, $fkStatus); }
    public function setStockAvailability($stockAvailability) { $this->setData(SELF::STOCK_AVAILABILITY, $stockAvailability); }
    public function setPrecioUnitario($precioUnitario) { $this->setData(SELF::PRECIO_UNITARIO, $precioUnitario); }
    public function setDescuentoTotal($descuentoTotal) { $this->setData(SELF::DESCUENTO_TOTAL, $descuentoTotal); }
    public function setIva($iva) { $this->setData(SELF::IVA, $iva); }
    public function setTipoFelicitacion($tipoFelicitacion) { $this->setData(SELF::TIPO_FELICITACION, $tipoFelicitacion); }
    public function setMensajeFelicitacion($mensajeFelicitacion) { $this->setData(SELF::MENSAJE_FELICITACION, $mensajeFelicitacion); }
    public function setDescuentos($descuentos) { $this->setData(SELF::DESCUENTOS, $descuentos); }




}
