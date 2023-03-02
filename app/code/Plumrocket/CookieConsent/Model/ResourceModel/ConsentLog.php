<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_CookieConsent
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Plumrocket\CookieConsent\Api\Data\ConsentLogInterface;

/**
 * @since 1.0.0
 */
class ConsentLog extends AbstractDb
{
    /**
     * Name of Main Table
     */
    const MAIN_TABLE_NAME = 'pr_cookie_consent_log';

    /**
     * Name of Primary Column
     */
    const ID_FIELD_NAME = 'consent_id';

    /**
     * @inheritDoc
     */
    protected $_serializableFields = [
        ConsentLogInterface::SETTINGS => [[], []],
    ];

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::ID_FIELD_NAME);
    }

    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\Document $consentLog
     */
    public function unserializeDataProviderDocumentFields(Document $consentLog)
    {
        foreach ($this->_serializableFields as $field => list($serializeDefault, $unserializeDefault)) {
            $this->_unserializeField($consentLog, $field, $unserializeDefault);
        }
    }
}
