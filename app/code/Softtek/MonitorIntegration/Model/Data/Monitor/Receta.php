<?php


namespace Softtek\MonitorIntegration\Model\Data\Monitor;


class Receta extends \Magento\Framework\Api\AbstractExtensibleObject implements \Softtek\MonitorIntegration\Api\Data\Monitor\RecetasInterface
{
    public function getUrl() {
        return $this->_get(SELF::URL);
    }

    public function setUrl($url) {
        $this->setData(SELF::URL, $url);
    }
}
