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

namespace Plumrocket\CookieConsent\Test\Unit\Model\User;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Plumrocket\CookieConsent\Api\Data\CategoryInterface;
use Plumrocket\CookieConsent\Api\GetUserConsentInterface;
use Plumrocket\CookieConsent\Model\User\IsOptIn;

/**
 * @since 1.0.0
 */
class IsOptInTest extends TestCase
{

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\CookieConsent\Api\GetUserConsentInterface
     */
    private $getUserConsentMock;

    /**
     * @var \Plumrocket\CookieConsent\Api\IsUserOptInInterface
     */
    private $isOptInModel;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->getUserConsentMock = $this->createMock(GetUserConsentInterface::class);

        $this->isOptInModel = $objectManager->getObject(
            IsOptIn::class,
            [
                'getUserConsent' => $this->getUserConsentMock,
            ]
        );
    }

    public function testNotHaveConsent()
    {
        $this->getUserConsentMock
            ->method('execute')
            ->willThrowException(new NotFoundException(__('User didnt make any consents.')));

        $this->assertFalse($this->isOptInModel->execute());
    }

    public function testHaveConsent()
    {
        $this->getUserConsentMock
            ->method('execute')
            ->willReturn(['test_essential', 'test_marketing']);

        $this->assertTrue($this->isOptInModel->execute());
    }

    public function testHaveAllConsent()
    {
        $this->getUserConsentMock
            ->method('execute')
            ->willReturn([CategoryInterface::ALL_CATEGORIES]);

        $this->assertTrue($this->isOptInModel->execute());
    }
}
