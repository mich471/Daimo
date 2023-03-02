<?php

namespace Softtek\Vendor\Block\Magento\Customer\Widget;

use Magento\Customer\Api\CustomerMetadataInterface;
use Softtek\Vendor\Model\Customer\Attribute\Source\TypeCompanyOption;
use Softtek\Vendor\Model\Customer\Attribute\Source\OperatingSegmentType;

class AdditionalCompanyInfo extends \Magento\Customer\Block\Widget\AbstractWidget
{
    /**
     * @var TypeCompanyOption
     */
    protected $optionsTypeCompany;

    protected $optionsOperatingSegment;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        CustomerMetadataInterface $customerMetadata,
        TypeCompanyOption $optionsTypeCompany,
        OperatingSegmentType $optionsOperatingSegment,
        array $data = []
    )
    {
        $this->optionsTypeCompany = $optionsTypeCompany;
        $this->optionsOperatingSegment = $optionsOperatingSegment;
        parent::__construct($context, $addressHelper, $customerMetadata, $data);
    }

    public function getOptionsTypeCompany()
    {
        return $this->optionsTypeCompany->getAllOptions();
    }

    public function getOptionsOperatingSegmentType()
    {
        return $this->optionsOperatingSegment->getAllOptions();
    }
}
