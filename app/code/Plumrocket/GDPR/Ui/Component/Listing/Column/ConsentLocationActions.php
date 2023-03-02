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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Ui\Component\Listing\Column;

class ConsentLocationActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /** Url path */
    const URL_PATH_EDIT = 'prgdpr/consent_location/edit';
    const URL_PATH_DELETE = 'prgdpr/consent_location/delete';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Plumrocket\GDPR\Api\ConsentLocationTypeInterface
     */
    private $consentLocationType;

    /**
     * ConsentLocationActions constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory
     * @param \Magento\Framework\UrlInterface                              $urlBuilder
     * @param \Plumrocket\GDPR\Api\ConsentLocationTypeInterface            $consentLocationType
     * @param array                                                        $components
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Plumrocket\GDPR\Api\ConsentLocationTypeInterface $consentLocationType,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->consentLocationType = $consentLocationType;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key => & $item) {
                $name = $this->getData('name');

                if ($this->consentLocationType->isSystem((int)$item['type'])) {
                    continue;
                }

                if (isset($item['location_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_EDIT,
                            ['location_id' => $item['location_id']]
                        ),
                        'label' => __('Edit')
                    ];
                }
            }
        }

        return $dataSource;
    }
}
