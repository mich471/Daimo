<?php


namespace Softtek\MonitorIntegration\Model\Data\Monitor;


class PosMessages extends \Magento\Framework\Api\AbstractExtensibleObject implements \Softtek\MonitorIntegration\Api\Data\Monitor\PosMessagesInterface
{
    public function getType() {
        return $this->_get(SELF::TYPE);
    }

    public function setType($type) {
        return $this->setData(SELF::TYPE, $type);
    }

    public function getContent() {
        return $this->_get(SELF::CONTENT);
    }

    public function setContent($content) {
        return $this->setData(SELF::CONTENT, $content);
    }

    public function getChannel() {
        return $this->_get(SELF::CHANNEL);
    }

    public function setChannel($channel) {
        return $this->setData(SELF::CHANNEL, $channel);
    }

}
