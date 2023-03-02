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

namespace Plumrocket\CookieConsent\Ui\DataProvider\Cookie\Category\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * @since 1.0.0
 */
class Key implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta): array
    {
        if (isset($meta['general']['children']['key'])) {
            $categoryKeyConfig = $meta['general']['children']['key']['arguments']['data']['config'];

            $categoryKeyConfig['notice'] = (string) __(
                'Use "Category Identifier" when you need to block cookies manually in the code. We recommend using' .
                ' the following identifiers for default Cookie Categories: "necessary", "preference", "statistic"' .
                ' and "marketing". See our' .
                ' <a href="http://wiki.plumrocket.com/Magento_2_GDPR_v1.x_Developers_Guide_and_API_Reference" ' .
                'target="_blank">Developer\'s Guide</a> for more info.'
            );

            $categoryKeyConfig['template'] = 'Plumrocket_CookieConsent/form/field';

            $meta['general']['children']['key']['arguments']['data']['config'] = $categoryKeyConfig;
        }

        return $meta;
    }
}
