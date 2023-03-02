<?php
/**
 * @package     Plumrocket_magento_2_3_6__1
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Checkbox\Validator;

use Plumrocket\DataPrivacyApi\Api\ConsentCheckboxesValidatorInterface;
use Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface;

class DefaultValidator implements ConsentCheckboxesValidatorInterface
{
    /**
     * @var \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface
     */
    private $consentCheckboxProvider;

    /**
     * @param \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxProviderInterface $consentCheckboxProvider
     */
    public function __construct(ConsentCheckboxProviderInterface $consentCheckboxProvider)
    {
        $this->consentCheckboxProvider = $consentCheckboxProvider;
    }

    /**
     * @ingeritdoc
     */
    public function isAcceptedAllRequiredCheckboxes(
        array $checkedConsentCheckboxIds,
        string $locationKey,
        int $customerId
    ): bool {
        $checkboxesToAgree = $this->consentCheckboxProvider->getCheckboxesToAgreeByLocation($customerId, $locationKey);
        foreach ($checkboxesToAgree as $checkbox) {
            $isCheckboxPresentedInConsents = in_array($checkbox->getId(), $checkedConsentCheckboxIds, false);
            if (!$isCheckboxPresentedInConsents && $checkbox->isRequiredForValidate()) {
                return false;
            }
        }
        return true;
    }
}
