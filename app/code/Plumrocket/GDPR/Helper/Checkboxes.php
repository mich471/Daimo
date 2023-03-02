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
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Helper;

use Magento\Framework\Exception\LocalizedException;
use Plumrocket\GDPR\Model\Config\Source\ConsentAction;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

class Checkboxes extends \Magento\Framework\App\Helper\AbstractHelper
{
    const POPUP_CLASSNAME = 'pr-inpopup';

    /**
     * @var array
     */
    private $checkboxes = null;

    /**
     * @var string[]|null
     */
    private $locationKeys;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\GDPR\Helper\Geo\Location
     */
    private $geoLocationHelper;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog
     */
    private $consentsLogResource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var \Plumrocket\GDPR\Api\CheckboxProviderInterface
     */
    private $checkboxProvider;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Consent\Location
     */
    private $locationResource;

    /**
     * Checkboxes constructor.
     *
     * @param \Plumrocket\GDPR\Helper\Data                          $dataHelper
     * @param \Plumrocket\GDPR\Helper\Geo\Location                  $geoLocationHelper
     * @param \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog      $consentsLogResource
     * @param \Magento\Framework\App\Helper\Context                 $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime           $dateTime
     * @param \Magento\Framework\Filter\FilterManager               $filterManager
     * @param \Magento\Customer\Helper\Session\CurrentCustomer      $currentCustomer
     * @param \Magento\Newsletter\Model\SubscriberFactory           $subscriberFactory
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManager
     * @param \Plumrocket\GDPR\Api\CheckboxProviderInterface        $checkboxProvider
     * @param \Magento\Framework\Serialize\SerializerInterface      $serializer
     * @param \Plumrocket\GDPR\Model\ResourceModel\Consent\Location $locationResource
     */
    public function __construct(
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper,
        \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog $consentsLogResource,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Plumrocket\DataPrivacyApi\Api\CheckboxProviderInterface $checkboxProvider,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Plumrocket\GDPR\Model\ResourceModel\Consent\Location $locationResource
    ) {
        $this->dataHelper = $dataHelper;
        $this->geoLocationHelper = $geoLocationHelper;
        $this->consentsLogResource = $consentsLogResource;
        $this->dateTime = $dateTime;
        $this->filterManager = $filterManager;
        $this->currentCustomer = $currentCustomer;
        $this->subscriberFactory = $subscriberFactory;

        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->remoteAddress = $context->getRemoteAddress();
        $this->checkboxProvider = $checkboxProvider;
        $this->serializer = $serializer;
        $this->locationResource = $locationResource;
    }

    /**
     * @return bool
     */
    public function isSubscribedCurrentCustomer()
    {
        $customerId = $this->currentCustomer->getCustomerId();

        if (!$customerId) {
            return false;
        }

        $subscriber = $this->subscriberFactory->create();

        return $subscriber->loadByCustomerId($customerId)->isSubscribed();
    }

    /**
     * @param $page
     * @return bool
     */
    public function canShowCheckboxes($page = null)
    {
        if (!$this->dataHelper->moduleEnabled()) {
            return false;
        }

        if (ConsentLocations::NEWSLETTER === (string)$page
            && $this->isSubscribedCurrentCustomer()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @deprecated since 3.1.0
     * @see
     *
     * @param null $locationKey
     * @param bool $withAlreadyChecked
     * @param bool $checkVersion
     * @param bool $addOldVersions
     * @return \Plumrocket\GDPR\Api\Data\CheckboxInterface[]
     */
    public function getCheckboxes(
        $locationKey = null,
        $withAlreadyChecked = false,
        $checkVersion = true,
        $addOldVersions = false
    ): array {
        if (!$this->canShowCheckboxes($locationKey)) {
            return [];
        }

        if ($addOldVersions) {
            return $this->checkboxProvider->getEnabledWithOldChecked(
                (int)$this->currentCustomer->getCustomerId()
            );
        }

        if (null === $this->checkboxes) {
            $this->checkboxes = [];
            foreach ($this->checkboxProvider->getEnabled() as $checkbox) {
                if (!$this->geoLocationHelper->isPassCheckboxGeoIPRestriction($checkbox)) {
                    continue;
                }

                if (!$withAlreadyChecked && $checkbox->isAlreadyChecked(0, $checkVersion)) {
                    continue;
                }

                foreach ($checkbox->getLocationKeys() as $location) {
                    $this->checkboxes[$location][$checkbox->getId()] = $checkbox;
                }
            }
        }

        if ($locationKey) {
            return array_key_exists($locationKey, $this->checkboxes) ? $this->checkboxes[$locationKey] : [];
        }

        $result = [];

        foreach ($this->checkboxes as $pageType => $checkboxes) {
            $result += $checkboxes;
        }

        return $result;
    }

    /**
     * @param $consentIds
     * @param null $locationKey
     * @param bool $withAlreadyChecked
     * @param bool $checkVersion
     * @return bool
     */
    public function isValidConsents(
        $consentIds,
        $locationKey = null,
        $withAlreadyChecked = false,
        $checkVersion = true
    ): bool {
        $checkboxes = $this->getCheckboxes($locationKey, $withAlreadyChecked, $checkVersion);

        if (empty($checkboxes)) {
            return true;
        }

        if (!is_array($consentIds)) {
            return false;
        }

        foreach ($checkboxes as $id => $checkbox) {
            $isCheckboxPresentedInConsents = in_array($id, $consentIds, false);

            if (!$isCheckboxPresentedInConsents && $checkbox->isRequiredForValidate()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $location
     * @return bool
     */
    public function isValidLocation($location)
    {
        if (null === $this->locationKeys) {
            $this->locationKeys = $this->locationResource->getAllLocationKeys();
        }

        return in_array($location, $this->locationKeys, true);
    }

    /**
     * @return int
     */
    public function getCurrentCustomerId()
    {
        return (int)$this->currentCustomer->getCustomerId();
    }

    /**
     * @return int
     */
    public function getCurrentWebsiteId()
    {
        return (int)$this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * @return string
     */
    public function getCurrentRemoteAddress()
    {
        return (string)$this->remoteAddress->getRemoteAddress();
    }

    /**
     * @param null $timestamp
     * @return string
     */
    public function getFormattedGmtDateTime($timestamp = null)
    {
        if (null === $timestamp) {
            $timestamp = $this->dateTime->gmtTimestamp();
        }

        return (string)date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * @param $column
     * @return mixed
     */
    public function getBaseDataByColumn($column)
    {
        $result = null;

        switch ($column) {
            case 'created_at':
                $result = $this->getFormattedGmtDateTime();
                break;
            case 'customer_id':
                $result = $this->getCurrentCustomerId();
                break;

            case 'website_id':
                $result = $this->getCurrentWebsiteId();
                break;

            case 'customer_ip':
                $result = $this->getCurrentRemoteAddress();
                break;

            case 'email':
                $result = $this->_getRequest()->getParam('email', '');
                break;
            case 'action':
                $result = ConsentAction::ACTION_ACCEPT_VALUE;
                break;
        }

        return $result;
    }

    /**
     * @param null $forceBaseData
     * @return array
     */
    public function getBaseDataForConsent($forceBaseData = null): array
    {
        $columns = [
            'created_at',
            'customer_id',
            'website_id',
            'customer_ip',
            'action',
        ];

        if (! $this->getCurrentCustomerId()) {
            $columns[] = 'email';
        }

        $result = [];
        foreach ($columns as $column) {
            $result[$column] = $forceBaseData[$column] ?? $this->getBaseDataByColumn($column);
        }

        return $result;
    }

    /**
     * @param $location
     * @param $consentIds
     * @param null $forceBaseData
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveMultipleConsents($location, $consentIds, $forceBaseData = null)
    {
        if (! $this->canDoSave($consentIds, $location, $forceBaseData)) {
            return 0;
        }

        $insertData = [];
        $checkboxes = $this->checkboxProvider->getEnabled(true);
        $baseRowData = $this->getBaseDataForConsent($forceBaseData);

        foreach ($consentIds as $consentId) {
            if (!is_string($consentId) && !is_numeric($consentId)) {
                continue;
            }

            if (array_key_exists($consentId, $checkboxes)) {
                $checkbox = $checkboxes[$consentId];

                if (!in_array($location, $checkbox->getLocationKeys(), true)) {
                    continue;
                }

                $insertData[] = $this->prepareData($location, $baseRowData, $checkbox);
            }
        }

        return !empty($insertData) ? $this->consentsLogResource->saveMultipleConsents($insertData) : 0;
    }

    /**
     * @param string $location
     * @param array $consents
     * @param null $forceBaseData
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveConsents(string $location, array $consents, $forceBaseData = null): int
    {
        if (! $this->canDoSave($consents, $location, $forceBaseData)) {
            return 0;
        }

        $insertData = [];
        $baseRowData = $this->getBaseDataForConsent($forceBaseData);

        foreach ($consents as $consent) {
            if ($consent->getLocationKeys() && !in_array($location, $consent->getLocationKeys(), true)) {
                continue;
            }

            $insertData[] = $this->prepareData($location, $baseRowData, $consent);
        }

        return !empty($insertData) ? $this->consentsLogResource->saveMultipleConsents($insertData) : 0;
    }

    /**
     * @param \Plumrocket\GDPR\Api\Data\CheckboxInterface[] $checkboxes
     * @param bool $useCheckedSign use for loading heavy data
     * @return bool|string
     */
    public function serialize(array $checkboxes, bool $useCheckedSign = false)
    {
        $checkboxesAsArrays = [];

        foreach ($checkboxes as $checkbox) {
            $checkboxData = $checkbox->extractArrayForJs();
            $checkboxData['agreeUrl'] = $this->_getUrl('prgdpr/consentpopups/confirm', ['_secure' => true]);

            if ($useCheckedSign) {
                $checkboxData['isAlreadyChecked'] = $checkbox->isAlreadyChecked();
            }

            $checkboxesAsArrays[] = $checkboxData;
        }

        return $this->serializer->serialize($checkboxesAsArrays);
    }

    /**
     * @param string $location
     * @param array $baseRowData
     * @param $consent
     * @return array
     */
    private function prepareData(string $location, array $baseRowData, $consent): array
    {
        $rowData = $baseRowData;
        $rowData['location'] = $location;
        $rowData['label'] = $this->filterManager->stripTags($consent->getLabel());
        $rowData['cms_page_id'] = $consent->getCmsPageId();
        $rowData['version'] = null;
        $rowData['checkbox_id'] = $consent->getId();
        $rowData['customer_id'] = $rowData['customer_id'] ?: null;

        if ($rowData['cms_page_id']) {
            $rowData['version'] = $consent->getCmsPageInfo()
                ? (string)$consent->getCmsPageInfo()['version']
                : '';
        }

        return $rowData;
    }

    /**
     * @param $consents
     * @param $location
     * @param $forceBaseData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function canDoSave($consents, $location, $forceBaseData): bool
    {
        if (!$this->dataHelper->moduleEnabled()
            || !is_array($consents)
        ) {
            return false;
        }

        if (!$this->isValidLocation($location)) {
            throw new LocalizedException(__('Undefined GDPR location for specified consents.'));
        }

        $baseRowData = $this->getBaseDataForConsent($forceBaseData);

        if (isset($forceBaseData['customer_id']) && ! $baseRowData['customer_id']) {
            throw new LocalizedException(__('Undefined Customer ID for specified consents.'));
        }

        return true;
    }

    /**
     * Created to simplify integrations
     * Must be used only in templates without classes and viewModels!
     * @since 2.0.0
     *
     * @param $data
     * @return string
     */
    public function serializeToJson($data): string
    {
        return $this->serializer->serialize($data);
    }
}
