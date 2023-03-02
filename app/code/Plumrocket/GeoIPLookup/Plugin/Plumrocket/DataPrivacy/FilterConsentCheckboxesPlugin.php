<?php
/**
 * @package     Plumrocket_GeoIPLookup
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\GeoIPLookup\Plugin\Plumrocket\DataPrivacy;

use Magento\Framework\Exception\LocalizedException;
use Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface;
use Plumrocket\GeoIPLookup\Api\GeoLocationValidatorInterface;
use Plumrocket\GeoIPLookup\Helper\Config;

/**
 * Integration with Data Privacy
 *
 * Filter checkboxes that is not passing geo ip restriction
 *
 * @since 1.2.5
 */
class FilterConsentCheckboxesPlugin
{
    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\GeoIPLookup\Api\GeoLocationValidatorInterface
     */
    private $geoLocationValidator;

    /**
     * @param \Plumrocket\GeoIPLookup\Helper\Config                     $config
     * @param \Plumrocket\GeoIPLookup\Api\GeoLocationValidatorInterface $geoLocationValidator
     */
    public function __construct(
        Config $config,
        GeoLocationValidatorInterface $geoLocationValidator
    ) {
        $this->config = $config;
        $this->geoLocationValidator = $geoLocationValidator;
    }

    /**
     * @param \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface $subject
     * @param \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[]         $result
     * @param int                                                             $customerId
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface[]
     */
    public function afterGetEnabledCustomerCheckboxes(
        ConsentCheckboxProviderInterface $subject,
        array $result,
        int $customerId
    ): array {
        if ($result && $this->config->isConfiguredForUse()) {
            try {
                $checkboxes = [];
                foreach ($result as $checkbox) {
                    $isAllowed = $this->geoLocationValidator->validateByMergedOptions(
                        $checkbox->getGeoTargeting(),
                        $checkbox->getGeoTargetingUsaStates()
                    );
                    if (! $isAllowed) {
                        continue;
                    }
                    $checkboxes[] = $checkbox;
                }
                return $checkboxes;
            } catch (LocalizedException $e) {
                return $result;
            }
        }

        return $result;
    }
}
