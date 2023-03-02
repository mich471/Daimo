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

namespace Plumrocket\CookieConsent\Test\Unit\Model\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Plumrocket\CookieConsent\Api\CanManageCookieInterface;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface;
use Plumrocket\CookieConsent\Api\GetUserConsentInterface;
use Plumrocket\CookieConsent\Api\IsUserOptInInterface;
use Plumrocket\CookieConsent\Helper\Config;
use Plumrocket\CookieConsent\Model\Category\IsAllowed;

/**
 * @since 1.0.0
 */
class IsAllowedTest extends TestCase
{
    /**
     * @var \Plumrocket\CookieConsent\Model\Category\IsAllowed
     */
    private $isAllowedModel;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\CookieConsent\Api\CanManageCookieInterface
     */
    private $canManageCookieMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\CookieConsent\Helper\Config
     */
    private $configMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\CookieConsent\Api\IsUserOptInInterface
     */
    private $isUserOptInMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\CookieConsent\Api\GetUserConsentInterface
     */
    private $getUserConsentMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\CookieConsent\Api\GetEssentialCategoryKeysInterface
     */
    private $getEssentialCategoryKeysMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->canManageCookieMock = $this->createMock(CanManageCookieInterface::class);
        $this->configMock = $this->createMock(Config::class);
        $this->isUserOptInMock = $this->createMock(IsUserOptInInterface::class);
        $this->getUserConsentMock = $this->createMock(GetUserConsentInterface::class);
        $this->getEssentialCategoryKeysMock = $this->createMock(GetEssentialCategoryKeysInterface::class);

        $this->isAllowedModel = $objectManager->getObject(
            IsAllowed::class,
            [
                'canManageCookie' => $this->canManageCookieMock,
                'getUserConsent' => $this->getUserConsentMock,
                'isUserOptIn' => $this->isUserOptInMock,
                'config' => $this->configMock,
                'getEssentialCategoryKeys' => $this->getEssentialCategoryKeysMock,
            ]
        );
    }

    public function testCannotManageCookie()
    {
        $this->canManageCookieMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $this->assertTrue($this->isAllowedModel->execute('test_category_key'));
    }

    public function testEssentialCategories()
    {
        $this->canManageCookieMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->getEssentialCategoryKeysMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(['test_essential_category_key_1']);

        $this->isUserOptInMock->expects($this->never())->method('execute');
        $this->getUserConsentMock->expects($this->never())->method('execute');

        $this->assertTrue($this->isAllowedModel->execute('test_essential_category_key_1'));
    }

    /**
     * @dataProvider canUseCookieBeforeOptInProvider
     *
     * @param bool $canUseCookieBeforeOptIn
     * @param bool $result
     */
    public function testNotOptIn(bool $canUseCookieBeforeOptIn, bool $result)
    {
        $this->canManageCookieMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->getEssentialCategoryKeysMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(['test_essential_category_key_1']);

        $this->isUserOptInMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $this->configMock
            ->expects($this->once())
            ->method('canUseCookieBeforeOptIn')
            ->willReturn($canUseCookieBeforeOptIn);

        $this->assertSame($this->isAllowedModel->execute('test_category_key'), $result);
    }

    public function canUseCookieBeforeOptInProvider(): array
    {
        return [
            [
                'canUseCookieBeforeOptIn' => true,
                'result'                  => true,
            ],
            [
                'canUseCookieBeforeOptIn' => false,
                'result'                  => false,
            ],
        ];
    }

    /**
     * @dataProvider consentProvider
     *
     * @param array $consent
     * @param bool  $result
     */
    public function testConsent(array $consent, bool $result)
    {
        $this->canManageCookieMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->getEssentialCategoryKeysMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(['test_essential_category_key_1']);

        $this->isUserOptInMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->getUserConsentMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn($consent);

        $this->assertSame($this->isAllowedModel->execute('test_category_key'), $result);
    }

    public function consentProvider(): array
    {
        return [
            [
                'consent' => ['test_category_key'],
                'result'  => true,
            ],
            [
                'consent' => [],
                'result'  => false,
            ],
            [
                'consent' => ['test_category_key_1'],
                'result'  => false,
            ],
            [
                'consent' => [CategoryInterface::ALL_CATEGORIES],
                'result'  => true,
            ],
            [
                'consent' => ['test_category_key_1', 'test_category_key_2'],
                'result'  => false,
            ],
        ];
    }
}
