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

namespace Plumrocket\CookieConsent\Ui\Component\Listing\Columns;

use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @since 1.0.0
 */
class Link extends Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @return array
     */
    private function getHrefConfig()
    {
        $data = [
            'path' => '*',
            'identifier' => 'entity_id',
            'source' => 'entity_id',
        ];

        $config = $this->getData('config');

        if ($config
            && isset($config['href'])
            && is_array($config['href'])
        ) {
            $hrefConfig = $config['href'];

            $data = array_merge($data, $hrefConfig);
        }

        return $data;
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
            foreach ($dataSource['data']['items'] as & $item) {
                $fieldName = $this->getData('name');
                $config = $this->getData('config');
                $hrefData = $this->getHrefConfig();
                if ($this->canAddLink($item, $config)) {
                    $titleSource = ! empty($config['title']['source'])
                        ? (string)$config['title']['source']
                        : false;
                    $title = $titleSource ? $item[$titleSource] : $item[$fieldName];
                    $href = $this->urlBuilder->getUrl($hrefData['path'], [
                        $hrefData['identifier'] => $item[$hrefData['source']]
                    ]);
                    $item[$fieldName] = '<a href="' . $href . '">' . $this->escaper->escapeHtml($title) . '</a>';
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     * @param array $config
     * @return bool
     */
    protected function canAddLink(array $item, array $config) : bool
    {
        $requiredField = ! empty($config['title']['require'])
            ? (string) $config['title']['require']
            : false;

        if ($requiredField) {
            return isset($item[$requiredField]) && $item[$requiredField];
        }

        return true;
    }
}
