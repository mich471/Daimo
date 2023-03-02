<?php
/**
 * @package     Plumrocket_magento_2_3_6__1
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\Model\Consent\Checkbox;

use \Plumrocket\DataPrivacy\Model\Consent\Location\Validator as ConsentLocationValidator;
use Plumrocket\DataPrivacyApi\Api\ConsentCheckboxesValidatorInterface;

class CompositeValidator implements ConsentCheckboxesValidatorInterface
{
    /**
     * @var \Plumrocket\DataPrivacy\Model\Consent\Location\Validator
     */
    private $consentLocationValidator;

    /**
     * @var \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxesValidatorInterface[]
     */
    private $consentCheckboxesValidators;

    /**
     * @param \Plumrocket\DataPrivacy\Model\Consent\Location\Validator $consentLocationValidator
     * @param \Plumrocket\DataPrivacyApi\Api\ConsentCheckboxesValidatorInterface[]    $consentCheckboxesValidators
     */
    public function __construct(
        ConsentLocationValidator $consentLocationValidator,
        array $consentCheckboxesValidators = []
    ) {
        $this->consentLocationValidator = $consentLocationValidator;
        $this->consentCheckboxesValidators = $consentCheckboxesValidators;
    }

    /**
     * @ingeritdoc
     */
    public function isAcceptedAllRequiredCheckboxes(
        array $checkedConsentCheckboxIds,
        string $locationKey,
        int $customerId
    ): bool {
        if (! $this->consentLocationValidator->isValid($locationKey)) {
            throw new \InvalidArgumentException((string) __('Invalid location "%1"', $locationKey));
        }

        $validator = $this->consentCheckboxesValidators[$locationKey] ?? $this->consentCheckboxesValidators['default'];
        return $validator->isAcceptedAllRequiredCheckboxes($checkedConsentCheckboxIds, $locationKey, $customerId);
    }
}
