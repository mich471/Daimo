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
 * @package     Plumrocket_GeoIPLookup
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\CookieConsent\Test\Unit\Model\Cookie;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Plumrocket\CookieConsent\Api\CanManageCookieInterface;
use Plumrocket\CookieConsent\Api\IsAllowedCategoryInterface;
use Plumrocket\CookieConsent\Api\IsUserOptInInterface;
use Plumrocket\CookieConsent\Helper\Config;
use Plumrocket\CookieConsent\Model\Cookie\IsAllowed;
use Plumrocket\CookieConsent\Model\Cookie\Name\GetTrueName;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetCategoryKey;
use Plumrocket\CookieConsent\Model\ResourceModel\Cookie\IsKnown;

/**
 * @since 1.0.0
 */
class IsAllowedTest extends TestCase
{
    /**
     * @var \Plumrocket\CookieConsent\Model\Cookie\IsAllowed
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
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\CookieConsent\Model\ResourceModel\Cookie\GetCategoryKey
     */
    private $getCategoryKeyMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\CookieConsent\Api\IsAllowedCategoryInterface
     */
    private $isAllowedCategoryMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->canManageCookieMock = $this->createMock(CanManageCookieInterface::class);
        $this->configMock = $this->createMock(Config::class);
        $this->isUserOptInMock = $this->createMock(IsUserOptInInterface::class);
        $this->getCategoryKeyMock = $this->createMock(GetCategoryKey::class);
        $this->isAllowedCategoryMock = $this->createMock(IsAllowedCategoryInterface::class);
        $getTrueNameMock = $this->createMock(GetTrueName::class);
        $getTrueNameMock->method('execute')->willReturnArgument(0);

        $isKnown = $objectManager->getObject(
            IsKnown::class,
            ['getCategoryKey' => $this->getCategoryKeyMock]
        );

        $this->isAllowedModel = $objectManager->getObject(
            IsAllowed::class,
            [
                'canManageCookie' => $this->canManageCookieMock,
                'config' => $this->configMock,
                'isUserOptIn' => $this->isUserOptInMock,
                'getCategoryKey' => $this->getCategoryKeyMock,
                'isAllowedCategory' => $this->isAllowedCategoryMock,
                'isKnownCookie' => $isKnown,
                'getTrueName' => $getTrueNameMock,
                'systemCookies' => [
                    'test-key' => 'TEST_SYSTEM_COOKIE_NAME',
                ]
            ]
        );
    }

    public function testSystemCookie()
    {
        self::assertTrue($this->isAllowedModel->execute('TEST_SYSTEM_COOKIE_NAME'));
    }

    public function testCannotManageCookie()
    {
        $this->canManageCookieMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn(false);

        $this->getCategoryKeyMock
            ->expects(self::never())
            ->method('execute');

        $this->isAllowedCategoryMock
            ->expects(self::never())
            ->method('execute');

        self::assertTrue($this->isAllowedModel->execute('test_cookie_name'));
    }

    /**
     * @dataProvider canUseCookieBeforeOptInProvider
     *
     * @param bool $canUseCookieBeforeOptIn
     * @param bool $isKnown
     * @param bool $isAllowedCategory
     * @param bool $result
     */
    public function testNotOptInUser(
        bool $canUseCookieBeforeOptIn,
        bool $isKnown,
        bool $isAllowedCategory,
        bool $result
    ) {
        $this->canManageCookieMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn(true);

        $this->isUserOptInMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn(false);

        $this->configMock
            ->expects(self::once())
            ->method('canUseCookieBeforeOptIn')
            ->willReturn($canUseCookieBeforeOptIn);

        $this->getCategoryKeyMock
            ->method('execute')
            ->willReturn($isKnown ? 'test_category' : '');

        $this->isAllowedCategoryMock
            ->method('execute')
            ->willReturn($isAllowedCategory);

        $this->configMock->expects(self::never())->method('canBlockUnknownCookie');

        self::assertSame($result, $this->isAllowedModel->execute('test_cookie_name'));
    }

    public function canUseCookieBeforeOptInProvider(): array
    {
        return [
            [
                'canUseCookieBeforeOptIn' => true,
                'isKnown'                 => false,
                'isAllowedCategory'       => false,
                'result'                  => true,
            ],
            [
                'canUseCookieBeforeOptIn' => false,
                'isKnown'                 => false,
                'isAllowedCategory'       => false,
                'result'                  => false,
            ],
            [
                'canUseCookieBeforeOptIn' => false,
                'isKnown'                 => true,
                'isAllowedCategory'       => false,
                'result'                  => false,
            ],
            [
                'canUseCookieBeforeOptIn' => false,
                'isKnown'                 => true,
                'isAllowedCategory'       => true,
                'result'                  => true,
            ],
        ];
    }

    /**
     * @dataProvider canBlockUnknownCookieProvider
     *
     * @param bool $canBlockUnknownCookie
     * @param bool $result
     */
    public function testUnknownCookie(bool $canBlockUnknownCookie, bool $result)
    {
        $this->canManageCookieMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn(true);

        $this->isUserOptInMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn(true);

        $this->getCategoryKeyMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn('');

        $this->configMock
            ->expects(self::once())
            ->method('canBlockUnknownCookie')
            ->willReturn($canBlockUnknownCookie);

        $this->isAllowedCategoryMock
            ->expects(self::never())
            ->method('execute');

        self::assertSame($result, $this->isAllowedModel->execute('test_cookie_name'));
    }

    public function canBlockUnknownCookieProvider(): array
    {
        return [
            [
                'canBlockUnknownCookie'   => true,
                'result'    => false,
            ],
            [
                'canBlockUnknownCookie'   => false,
                'result'    => true,
            ],
        ];
    }

    /**
     * @dataProvider isAllowedCategoryProvider
     *
     * @param bool $isAllowedCategory
     * @param bool $result
     */
    public function testCookieCategoryValidation(bool $isAllowedCategory, bool $result)
    {
        $this->canManageCookieMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn(true);

        $this->isUserOptInMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn(true);

        $this->getCategoryKeyMock
            ->expects(self::exactly(2))
            ->method('execute')
            ->willReturn('test_category');

        $this->configMock
            ->expects(self::never())
            ->method('canBlockUnknownCookie');

        $this->isAllowedCategoryMock
            ->expects(self::once())
            ->method('execute')
            ->willReturn($isAllowedCategory);

        self::assertSame($result, $this->isAllowedModel->execute('test_cookie_name'));
    }

    public function isAllowedCategoryProvider(): array
    {
        return [
            [
                'isAllowedCategory' => true,
                'result'            => true,
            ],
            [
                'isAllowedCategory' => false,
                'result'            => false,
            ],
        ];
    }
}
