<?php
/**
 * @package     Plumrocket_DataPrivacy
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\DataPrivacy\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Plumrocket\GDPR\Helper\Checkboxes;
use Plumrocket\GDPR\Helper\Notifys;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

class DataPrivacy implements SectionSourceInterface
{
    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxes;

    /**
     * @var \Plumrocket\GDPR\Helper\Notifys
     */
    private $notifys;

    /**
     * @param \Plumrocket\GDPR\Helper\Checkboxes $checkboxes
     * @param \Plumrocket\GDPR\Helper\Notifys    $notifys
     */
    public function __construct(
        Checkboxes $checkboxes,
        Notifys $notifys
    ) {
        $this->checkboxes = $checkboxes;
        $this->notifys = $notifys;
    }

    /**
     * {@inheritDoc}
     */
    public function getSectionData(): array
    {
        $missedRequiredRegistrationCheckboxes = $this->checkboxes->getCheckboxes(
            ConsentLocations::REGISTRATION,
            false,
            false
        );

        $checkboxesPages = [];

        foreach ($missedRequiredRegistrationCheckboxes as $key => $checkbox) {
            if (! $checkbox->isRequiredForValidate()) {
                unset($missedRequiredRegistrationCheckboxes[$key]);
                continue;
            }

            if ($checkbox->getCmsPageId()) {
                $checkboxesPages[] = $checkbox->getCmsPageId();
            }
        }

        $countNotifies = count($this->notifys->getNotifys());
        return [
            'countPopups' => count($checkboxesPages),
            'countNotifies' => $countNotifies,
            'countNotifys' => $countNotifies, // old field name, will be removed in 4.0
        ];
    }
}
