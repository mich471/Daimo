<?php


namespace Softtek\MonitorIntegration\Model\Data\Monitor;


class RemisionPayload extends \Magento\Framework\Api\AbstractExtensibleObject implements \Softtek\MonitorIntegration\Api\Data\Monitor\RemisionPayload
{

    public function getOrderNumber() { return $this->_get(SELF::ORDER_NUMBER); }
    public function getCityName() { return $this->_get(SELF::CITY_NAME); }
    public function getColinaName() { return $this->_get(SELF::COLINA_NAME); }
    public function getCreatedAt() { return $this->_get(SELF::CREATED_AT); }
    public function getEmail() { return $this->_get(SELF::EMAIL); }
    public function getExternalNumber() { return $this->_get(SELF::EXTERNAL_NUMBER); }
    public function getFirstName() { return $this->_get(SELF::FIRST_NAME); }
    public function getLastName() { return $this->_get(SELF::LAST_NAME); }
    public function getMunicipioName() { return $this->_get(SELF::MUNICIPIO_NAME); }
    public function getPhoneNumber() { return $this->_get(SELF::PHONE_NUMBER); }
    public function getStateName() { return $this->_get(SELF::STATE_NAME); }
    public function getStreetName() { return $this->_get(SELF::STREET_NAME); }
    public function getTipoEnvio() { return $this->_get(SELF::TIPO_ENVIO); }
    public function getZipCode() { return $this->_get(SELF::ZIP_CODE); }
    public function getClickCollect() { return $this->_get(SELF::CLICK_COLLECT); }
    public function getEventType() { return $this->_get(SELF::EVENT_TYPE); }
    public function getInternalNumber() { return $this->_get(SELF::INTERNAL_NUMBER); }
    public function getCodigoComercio() { return $this->_get(SELF::CODIGO_COMERCIO); }
    public function getRut() { return $this->_get(SELF::RUT); }
    public function getLatDireccion() { return $this->_get(SELF::LAT_DIRECCION); }
    public function getLongDireccion() { return $this->_get(SELF::LONG_DIRECCION); }
    public function getHomeType() { return $this->_get(SELF::HOME_TYPE); }
    public function getInitialHour() { return $this->_get(SELF::INITIAL_HOUR); }
    public function getFinalHour() { return $this->_get(SELF::FINAL_HOUR); }
    public function getTransactionCode() { return $this->_get(SELF::TRANSACTION_CODE); }
    public function getTotalRemision() { return $this->_get(SELF::TOTAL_REMISION); }
    public function getCurrencyIsocode() { return $this->_get(SELF::CURRENCY_ISOCODE); }
    public function getCostoDespacho() { return $this->_get(SELF::COSTO_DESPACHO); }
    public function getRequiereValidacionQf() { return $this->_get(SELF::REQUIERE_VALIDACION_QF); }
    public function getItems() { return $this->_get(SELF::ITEMS); }
    public function getMediosPago() { return $this->_get(SELF::MEDIOS_PAGO); }
    public function getImgRecetas() { return $this->_get(SELF::IMGRECETAS); }
    public function getPosMessages() {return $this->_get(SELF::POS_MESSAGES); }

    public function setOrderNumber($orderNumber) { $this->setData(SELF::ORDER_NUMBER, $orderNumber); }
    public function setCityName($cityName) { $this->setData(SELF::CITY_NAME, $cityName); }
    public function setColinaName($colinaName) { $this->setData(SELF::COLINA_NAME, $colinaName); }
    public function setCreatedAt($createdAt) { $this->setData(SELF::CREATED_AT, $createdAt); }
    public function setEmail($email) { $this->setData(SELF::EMAIL, $email); }
    public function setExternalNumber($externalNumber) { $this->setData(SELF::EXTERNAL_NUMBER, $externalNumber); }
    public function setFirstName($firstName) { $this->setData(SELF::FIRST_NAME, $firstName); }
    public function setLastName($lastName) { $this->setData(SELF::LAST_NAME, $lastName); }
    public function setMunicipioName($municipioName) { $this->setData(SELF::MUNICIPIO_NAME, $municipioName); }
    public function setPhoneNumber($phoneNumber) { $this->setData(SELF::PHONE_NUMBER, $phoneNumber); }
    public function setStateName($stateName) { $this->setData(SELF::STATE_NAME, $stateName); }
    public function setStreetName($streetName) { $this->setData(SELF::STREET_NAME, $streetName); }
    public function setTipoEnvio($tipoEnvio) { $this->setData(SELF::TIPO_ENVIO, $tipoEnvio); }
    public function setZipCode($zipCode) { $this->setData(SELF::ZIP_CODE, $zipCode); }
    public function setClickCollect($clickCollect) { $this->setData(SELF::CLICK_COLLECT, $clickCollect); }
    public function setEventType($eventType) { $this->setData(SELF::EVENT_TYPE, $eventType); }
    public function setInternalNumber($internalNumber) { $this->setData(SELF::INTERNAL_NUMBER, $internalNumber); }
    public function setCodigoComercio($codigoComercio) { $this->setData(SELF::CODIGO_COMERCIO, $codigoComercio); }
    public function setRut($rut) { $this->setData(SELF::RUT, $rut); }
    public function setLatDireccion($latDireccion) { $this->setData(SELF::LAT_DIRECCION, $latDireccion); }
    public function setLongDireccion($longDireccion) { $this->setData(SELF::LONG_DIRECCION, $longDireccion); }
    public function setHomeType($homeType) { $this->setData(SELF::HOME_TYPE, $homeType); }
    public function setInitialHour($initialHour) { $this->setData(SELF::INITIAL_HOUR, $initialHour); }
    public function setFinalHour($finalHour) { $this->setData(SELF::FINAL_HOUR, $finalHour); }
    public function setTransactionCode($transactionCode) { $this->setData(SELF::TRANSACTION_CODE, $transactionCode); }
    public function setTotalRemision($totalRemision) { $this->setData(SELF::TOTAL_REMISION, $totalRemision); }
    public function setCurrencyIsocode($currencyIsocode) { $this->setData(SELF::CURRENCY_ISOCODE, $currencyIsocode); }
    public function setCostoDespacho($costoDespacho) { $this->setData(SELF::COSTO_DESPACHO, $costoDespacho); }
    public function setRequiereValidacionQf($requiereValidacionQf) { $this->setData(SELF::REQUIERE_VALIDACION_QF, $requiereValidacionQf); }
    public function setItems($items) { $this->setData(SELF::ITEMS, $items);}
    public function setMediosPago($mediosPago) { $this->setData(SELF::MEDIOS_PAGO, $mediosPago); }
    public function setImgRecetas($imgRecetas) { $this->setData(SELF::IMGRECETAS, $imgRecetas); }
    public function setPosMessages($posMessages) {$this->setData(SELF::POS_MESSAGES, $posMessages); }
}
