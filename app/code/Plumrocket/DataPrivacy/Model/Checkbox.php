<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model;

use Magento\Catalog\Model\AbstractModel;
use Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface as DataCheckboxInterface;
use Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface;
use Plumrocket\GDPR\Model\Config\Source\ConsentAction;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * @method $this setData($key, $value = null)
 */
class Checkbox extends AbstractModel implements
    DataCheckboxInterface
{

    const ENTITY = 'prgdpr_checkbox';

    const CACHE_TAG = 'prgdpr_checkbox';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'prgdpr_checkbox'; //@codingStandardsIgnoreLine

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'checkbox'; //@codingStandardsIgnoreLine

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG; //@codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var ResourceModel\ConsentsLog\CollectionFactory
     */
    private $consentsLogCollectionFactory;

    /**
     * @var ResourceModel\Revision\CollectionFactory
     */
    private $revisionCollectionFactory;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var bool
     */
    private $isLabelFormated = false;

    /**
     * @var ResourceModel\Revision\History\CollectionFactory
     */
    private $revisionHistoryCollectionFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * @var \Plumrocket\GDPR\Model\ConsentsLog
     */
    private $lastConsentLog;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterfaceFactory
     */
    private $policyFactory;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\ConvertConsentCheckboxToArrayInterface
     */
    private $convertConsentCheckboxToArray;

    /**
     * @param \Magento\Framework\Model\Context                                        $context
     * @param \Magento\Framework\Registry                                             $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory                       $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory                            $customAttributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface                              $storeManager
     * @param \Magento\Framework\UrlInterface                                         $urlBuilder
     * @param \Magento\Customer\Helper\Session\CurrentCustomer                        $currentCustomer
     * @param \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory      $consentsLogCollectionFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory         $revisionCollectionFactory
     * @param \Magento\Cms\Api\PageRepositoryInterface                                $pageRepository
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\History\CollectionFactory $revisionHistoryCollectionFactory
     * @param \Magento\Cms\Model\Template\FilterProvider                              $filterProvider
     * @param \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterfaceFactory              $policyFactory
     * @param \Plumrocket\DataPrivacyApi\Api\ConvertConsentCheckboxToArrayInterface   $convertConsentCheckboxToArray
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null            $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                      $resourceCollection
     * @param array                                                                   $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory $consentsLogCollectionFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $revisionCollectionFactory,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\History\CollectionFactory $revisionHistoryCollectionFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterfaceFactory $policyFactory,
        \Plumrocket\DataPrivacyApi\Api\ConvertConsentCheckboxToArrayInterface $convertConsentCheckboxToArray,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $storeManager,
            $resource,
            $resourceCollection,
            $data
        );
        $this->urlBuilder = $urlBuilder;
        $this->currentCustomer = $currentCustomer;
        $this->consentsLogCollectionFactory = $consentsLogCollectionFactory;
        $this->revisionCollectionFactory = $revisionCollectionFactory;
        $this->pageRepository = $pageRepository;
        $this->revisionHistoryCollectionFactory = $revisionHistoryCollectionFactory;
        $this->filterProvider = $filterProvider;
        $this->policyFactory = $policyFactory;
        $this->convertConsentCheckboxToArray = $convertConsentCheckboxToArray;
    }

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()// @codingStandardsIgnoreLine we need to extend parent method
    {
        $this->_init(\Plumrocket\GDPR\Model\ResourceModel\Checkbox::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function isRequiredForValidate(): bool
    {
        return $this->getStatus() && $this->getRequire();
    }

    /**
     * Check if customer already check this checkbox
     */
    public function isAlreadyChecked($customerId = 0, $checkVersion = true): bool
    {
        /** 'action' field available only for old version of checkboxes */
        if ($this->getData('action')) {
            return true;
        }

        $consentLog = $this->getLastRelatedConsentLog($customerId, $checkVersion);

        return null !== $consentLog && ConsentAction::ACTION_ACCEPT_VALUE === (int) $consentLog->getAction();
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): bool
    {
        return (bool) $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(bool $status): DataCheckboxInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getLocationKeys(): array
    {
        return (array) $this->getData(self::LOCATION_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setLocationKeys(array $locationKeys): DataCheckboxInterface
    {
        return $this->setData(self::LOCATION_KEY, $locationKeys);
    }

    /**
     * Check if checkbox is use specific location.
     *
     * @param string $locationKey
     * @return bool
     */
    public function isUsedInLocation(string $locationKey): bool
    {
        return in_array($locationKey, $this->getLocationKeys(), true);
    }

    /**
     * @inheritDoc
     */
    public function getLabel(bool $formatLabel = true): string
    {
        if ($formatLabel) {
            $this->formatLabel();
        }

        return (string) $this->getData(self::LABEL);
    }

    /**
     * @inheritDoc
     */
    public function setLabel(string $label): DataCheckboxInterface
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @inheritDoc
     */
    public function getCmsPageId(): int
    {
        return (int) $this->getData(self::CMS_PAGE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCmsPageId($cmsPageId): DataCheckboxInterface
    {
        return $this->setData(self::CMS_PAGE_ID, (int) $cmsPageId);
    }

    /**
     * @inheritDoc
     */
    public function getRequire(): bool
    {
        return (bool) $this->getData(self::REQUIRE);
    }

    /**
     * @inheritDoc
     */
    public function setRequire(bool $isRequired): DataCheckboxInterface
    {
        return $this->setData(self::REQUIRE, $isRequired);
    }

    /**
     * @inheritDoc
     */
    public function getGeoTargeting(): array
    {
        return (array) $this->getData(self::GEO_TARGETING);
    }

    /**
     * @inheritDoc
     */
    public function getGeoTargetingUsaStates(): array
    {
        $data = $this->getData(self::GEO_TARGETING_USA_STATES);

        return explode(",", $data);
    }

    /**
     * @inheritDoc
     */
    public function setGeoTargeting(array $geoTargeting): DataCheckboxInterface
    {
        return $this->setData(self::GEO_TARGETING, $geoTargeting);
    }

    /**
     * @inheritDoc
     */
    public function getInternalNote(): string
    {
        return (string) $this->getData(self::INTERNAL_NOTE);
    }

    /**
     * @inheritDoc
     */
    public function setInternalNote(string $internalNote): DataCheckboxInterface
    {
        return $this->setData(self::INTERNAL_NOTE, $internalNote);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        if ($this->hasData(self::STORE_ID)) {
            return $this->getData(self::STORE_ID);
        }
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId): DataCheckboxInterface
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get url for agree with checkbox
     */
    public function getAgreeUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'prgdpr/consentpopups/confirm',
            ['_secure' => true]
        );
    }

    /**
     * Retrieve info about cmp page id
     *
     * @param null $key
     * @return array|false
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getCmsPageInfo($key = null)
    {
        $pageInfo = $this->getData('cms_page_info');
        $pageId = $this->getCmsPageId();

        if ($pageId && ! $pageInfo) {
            try {
                /** @var \Magento\Cms\Api\Data\PageInterface $cmsPage */
                $cmsPage = $this->pageRepository->getById($pageId);
                /** @var \Plumrocket\GDPR\Model\ResourceModel\Revision\Collection $revisionCollection */
                $revisionCollection = $this->revisionCollectionFactory->create();
                $revision = $revisionCollection->getRevisionByPageId($pageId);

                /** 'version' exist only for old consents */
                if ($revision->getId() && null !== $this->getData('version')) {
                    /** @var ResourceModel\Revision\History\Collection $revisionHistoryCollection */
                    $revisionHistoryCollection = $this->revisionHistoryCollectionFactory->create();
                    $documentVersion = $this->getData('version');

                    $cmsPage->setContent(
                        $revisionHistoryCollection->getRevisionByParams([
                                                                            'revision_id' => $revision->getId(),
                                                                            'version'     => $documentVersion,
                                                                        ])->getContent()
                    );
                } else {
                    $documentVersion = $revision->getData('document_version');
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return false;
            }

            $this->setCmsPageInfo(
                [
                    'title'   => $cmsPage->getTitle(),
                    'url'     => $this->urlBuilder->getUrl(
                        $cmsPage->getIdentifier(),
                        ['_secure' => true]
                    ),
                    'content' => $cmsPage->getContent(),
                    'version' => $documentVersion ?: '',
                ]
            );
        }

        $pageInfo = $this->getData('cms_page_info');

        if ($pageId && $key) {
            if (! isset($pageInfo[$key])) {
                throw new \Magento\Framework\Exception\NotFoundException(__('Field with key "%1" is not found', $key));
            }

            return $pageInfo[$key];
        }

        return $pageInfo;
    }

    /**
     * @param array $cmsPageInfo
     * @return \Plumrocket\GDPR\Model\Checkbox
     */
    public function setCmsPageInfo(array $cmsPageInfo): DataCheckboxInterface
    {
        return $this->setData('cms_page_info', $cmsPageInfo);
    }

    /**
     * @deprecated since 3.1.0
     * @see \Plumrocket\DataPrivacyApi\Api\ConvertConsentCheckboxToArrayInterface::execute
     * @return array
     */
    public function extractArrayForJs(): array
    {
        $data = $this->convertConsentCheckboxToArray->execute($this);

        $data['cms_page'] = $data['policy'];
        $data['createdAt'] = $this->getLastRelatedConsentLog()
            ? $this->getLastRelatedConsentLog()->getCreatedAt()
            : '';

        return $data;
    }

    /**
     * @return bool
     */
    public function canDecline(): bool
    {
        return ! ($this->isRequiredForValidate() && $this->isUsedInLocation(ConsentLocations::REGISTRATION));
    }

    /**
     * @return bool
     */
    public function isUsePopup(): bool
    {
        return false !== strpos(
                (string) $this->getData(self::LABEL),
                \Plumrocket\GDPR\Helper\Checkboxes::POPUP_CLASSNAME
            );
    }

    /**
     * Collect data about cms page with selected in checkbox
     *
     * @return DataCheckboxInterface
     */
    private function formatLabel(): DataCheckboxInterface
    {
        if ($this->isLabelFormated) {
            return $this;
        }

        $sourceLabel = (string) $this->getData(self::LABEL);

        if ($this->getCmsPageId()) {
            try {
                $checkboxLabel = str_replace(
                    '{{url}}',
                    $this->getCmsPageInfo()['url'],
                    $sourceLabel
                );

                // TODO: Move this logic to frontend, it lets define action by configs of js components
                if ($this->isUsePopup()) {
                    $searchStr = 'class=';
                    $replaceStr = 'data-bind="attr: {\'data-checkboxid\':'
                        . ' $parent.getCheckboxId($parentContext, consentId)}, '
                        . 'click: function(data, event) '
                        . '{return $parent.showContent(data, event);}" class=';
                    $checkboxLabel = str_replace($searchStr, $replaceStr, $checkboxLabel);
                }
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
                $checkboxLabel = $sourceLabel;
                $this->setData('cms_page_info', false);
            }
        } else {
            $checkboxLabel = str_replace(
                ['{{url}}', 'target="_blank"'],
                ['JavaScript:void(0);', ''],
                $sourceLabel
            );
            $this->setData('cms_page_info', false);
        }

        $this->isLabelFormated = true;
        $this->setLabel($checkboxLabel);

        return $this;
    }

    /**
     * @param null $customerId
     * @param bool $checkVersion
     * @return bool|\Plumrocket\GDPR\Model\ConsentsLog
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function getLastRelatedConsentLog($customerId = null, $checkVersion = true)
    {
        if ((null === $this->lastConsentLog
                || ! $this->lastConsentLog->getId())
            && ($customerId
                || ($customerId = (int) $this->currentCustomer->getCustomerId()))
        ) {
            /** @var \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\Collection $consentsLogCollection */
            $consentsLogCollection = $this->consentsLogCollectionFactory->create()
                                                                        ->addFieldToFilter(
                                                                            'customer_id', ['eq' => $customerId]
                                                                        )
                                                                        ->setOrder('created_at', 'DESC')
                                                                        ->setPageSize(1);

            if ($this->getCmsPageInfo()) {
                $consentsLogCollection->addFieldToFilter('cms_page_id', ['eq' => $this->getCmsPageId()]);
                if ($checkVersion) {
                    if ('' === $this->getCmsPageInfo()['version']) { // if page didn't save, version might be null
                        $consentsLogCollection->addFieldToFilter(
                            ['version', 'version'],
                            [
                                ['is' => new \Zend_Db_Expr('null')],
                                ['eq' => ''],
                            ]
                        );
                    } else {
                        $consentsLogCollection->addFieldToFilter(
                            'version',
                            ['eq' => $this->getCmsPageInfo()['version']]
                        );
                    }
                }
            } else {
                $consentsLogCollection->addFieldToFilter('checkbox_id', $this->getId());
            }

            $this->lastConsentLog = $consentsLogCollection->getFirstItem();
        }

        return $this->lastConsentLog;
    }

    public function getPolicy(): ?PolicyInterface
    {
        if ($this->getData('policy_cache')) {
            return $this->getData('policy_cache');
        }

        $cms = $this->getCmsPageInfo();
        if (! $cms) {
            return null;
        }

        /** @var \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface $policy */
        $policy = $this->policyFactory->create();

        $policy->setId(0);
        $policy->setTitle($cms['title']);
        $policy->setContent($cms['content']);
        $policy->setUrlKey($cms['url']);
        $policy->setVersion($cms['version']);
        $policy->setIsCmsPage(true);
        $policy->setCmsPageId($this->getCmsPageId());

        $this->setData('policy_cache', $policy);

        return $policy;
    }
}
