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

namespace Plumrocket\GeoIPLookup\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Plumrocket\GeoIPLookup\Api\LocationsListInterface;
use Plumrocket\GeoIPLookup\Model\GeoIPLookup;
use Plumrocket\GeoIPLookup\Model\GeoLocationValidator;
use Plumrocket\GeoIPLookup\Model\LocationsList;

/**
 * @since 1.2.0
 */
class GeoLocationValidatorTest extends TestCase
{
    const CALIFORNIA = 'California';

    /**
     * @var \Plumrocket\GeoIPLookup\Model\GeoLocationValidator
     */
    private $validateLocation;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Plumrocket\GeoIPLookup\Model\GeoIPLookup
     */
    private $geoIPLookup;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->geoIPLookup = $this->createMock(GeoIPLookup::class);

        $locationsList = $objectManager->getObject(LocationsList::class);

        $this->validateLocation = $objectManager->getObject(
            GeoLocationValidator::class,
            [
                'geoIPLookup' => $this->geoIPLookup,
                'locationsList' => $locationsList
            ]
        );
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testDisabledExtension()
    {
        $this->geoIPLookup
            ->expects($this->once())
            ->method('canUse')
            ->willReturn(false);

        $this->expectException(LocalizedException::class);

        $this->validateLocation->validate([], []);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testValidateEmptyLocation()
    {
        $this->geoIPLookup
            ->expects($this->once())
            ->method('canUse')
            ->willReturn(true);

        $this->geoIPLookup
            ->expects($this->never())
            ->method('getCountryCode');

        $this->geoIPLookup
            ->expects($this->never())
            ->method('getCurrentCountryState');

        $this->geoIPLookup
            ->expects($this->never())
            ->method('isInEuropeanUnion');

        $this->assertTrue($this->validateLocation->validate([], []));
    }

    /**
     * @dataProvider allRegionProvider
     *
     * @param array $regions
     * @param array $countries
     * @param array $usaStates
     * @param bool  $result
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testAllRegion(array $regions, array $countries, array $usaStates, bool $result)
    {
        $this->geoIPLookup
            ->expects($this->once())
            ->method('canUse')
            ->willReturn(true);

        $this->geoIPLookup
            ->expects($this->never())
            ->method('getCountryCode');

        $this->geoIPLookup
            ->expects($this->never())
            ->method('getCurrentCountryState');

        $this->geoIPLookup
            ->expects($this->never())
            ->method('isInEuropeanUnion');

        $this->assertSame($result, $this->validateLocation->validate($regions, $countries, $usaStates));
    }

    public function allRegionProvider(): array
    {
        return [
            [
                'regions'   => [LocationsListInterface::ALL],
                'countries' => [],
                'usaStates' => [],
                'result'    => true,
            ],
            [
                'regions'   => [LocationsListInterface::ALL],
                'countries' => ['AU'],
                'usaStates' => [],
                'result'    => true,
            ],
            [
                'regions'   => [LocationsListInterface::ALL],
                'countries' => ['AU'],
                'usaStates' => [LocationsListInterface::USA_STATE_CALIFORNIA],
                'result'    => true,
            ],
        ];
    }

    /**
     * @dataProvider regionEUProvider
     *
     * @param string      $case
     * @param array       $regions
     * @param array       $countries
     * @param bool|null   $isInEU
     * @param string|null $countryCode
     * @param bool        $result
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testRegionsEURegion(
        string $case,
        array $regions,
        array $countries,
        $isInEU,
        $countryCode,
        bool $result
    ) {
        $this->geoIPLookup
            ->expects($this->once())
            ->method('canUse')
            ->willReturn(true);

        $this->geoIPLookup
            ->expects($this->never())
            ->method('getCurrentCountryState');

        $this->geoIPLookup
            ->method('getCountryCode')
            ->willReturn($countryCode);

        $this->geoIPLookup
            ->expects($this->once())
            ->method('isInEuropeanUnion')
            ->willReturn($isInEU);

        $this->assertSame($result, $this->validateLocation->validate($regions, $countries), $case);
    }

    public function regionEUProvider(): array
    {
        return [
            [
                'case'              => 'EU <=> Country in EU',
                'regions'           => [LocationsListInterface::EU],
                'countries'         => [],
                'isInEU'            => true,
                'countryCode'       => 'DE',
                'result'            => true,
            ],
            [
                'case'              => 'EU + Country in EU <=> Same country',
                'regions'           => [LocationsListInterface::EU],
                'countries'         => ['DE'],
                'isInEU'            => true,
                'countryCode'       => 'DE',
                'result'            => true,
            ],
            [
                'case'              => 'EU + Country not in EU <=> Another country',
                'regions'           => [LocationsListInterface::EU],
                'countries'         => ['AU'],
                'isInEU'            => false,
                'countryCode'       => 'CA',
                'result'            => false,
            ],
            [
                'case'              => 'EU + Country not in EU <=> Same country',
                'regions'           => [LocationsListInterface::EU],
                'countries'         => ['AU'],
                'isInEU'            => false,
                'countryCode'       => 'AU',
                'result'            => true,
            ],
            [
                'case'              => 'EU + Country not in EU <=> Country in EU',
                'regions'           => [LocationsListInterface::EU],
                'countries'         => ['AU'],
                'isInEU'            => true,
                'countryCode'       => 'DE',
                'result'            => true,
            ],
            [
                'case'              => 'EU + Country not in EU <=> Could not detect location',
                'regions'           => [LocationsListInterface::EU],
                'countries'         => ['AU'],
                'isInEU'            => null,
                'countryCode'       => null,
                'result'            => false,
            ],
        ];
    }

    /**
     * @dataProvider regionUnknownProvider
     *
     * @param string $case
     * @param array  $regions
     * @param array  $countries
     * @param        $isInEU
     * @param        $countryCode
     * @param bool   $result
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testUnknownRegion(
        string $case,
        array $regions,
        array $countries,
        $isInEU,
        $countryCode,
        bool $result
    ) {
        $this->geoIPLookup
            ->expects($this->once())
            ->method('canUse')
            ->willReturn(true);

        $this->geoIPLookup
            ->method('isInEuropeanUnion')
            ->willReturn($isInEU);

        $this->geoIPLookup
            ->expects($this->once())
            ->method('getCountryCode')
            ->willReturn($countryCode);

        $this->geoIPLookup
            ->expects($this->never())
            ->method('getCurrentCountryState')
            ->willReturn(null);

        $this->assertSame($result, $this->validateLocation->validate($regions, $countries, []), $case);
    }

    public function regionUnknownProvider(): array
    {
        return [
            [
                'case' => 'Unknown <=> some country',
                'regions' => [LocationsListInterface::UNKNOWN],
                'countries' => [],
                'isInEU'  => true,
                'countryCode' => 'FR',
                'result'  => false,
            ],
            [
                'case' => 'Unknown <=> Unknown',
                'regions' => [LocationsListInterface::UNKNOWN],
                'countries' => [],
                'isInEU'  => null,
                'in_array(LocationsListInterface::UNKNOWN, $regions, true)' => null,
                'result'  => true,
            ],
            [
                'case' => 'EU + Unknown <=> Unknown',
                'regions' => [LocationsListInterface::UNKNOWN, LocationsListInterface::EU],
                'countries' => [],
                'isInEU'  => null,
                'countryCode' => null,
                'result'  => true,
            ],
            [
                'case' => 'EU + Unknown + Country not in EU <=> Another country',
                'regions' => [LocationsListInterface::UNKNOWN, LocationsListInterface::EU],
                'countries' => ['AU'],
                'isInEU'  => false,
                'countryCode' => ['CA'],
                'result'  => false,
            ],
        ];
    }

    /**
     * @dataProvider countiesProvider
     *
     * @param string $case
     * @param array  $countries
     * @param string $countryCode
     * @param bool   $result
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testCountries(string $case, array $countries, string $countryCode, bool $result)
    {
        $this->geoIPLookup
            ->expects($this->once())
            ->method('canUse')
            ->willReturn(true);

        $this->geoIPLookup
            ->expects($this->once())
            ->method('getCountryCode')
            ->willReturn($countryCode);

        $this->geoIPLookup
            ->expects($this->never())
            ->method('getCurrentCountryState');

        $this->geoIPLookup
            ->expects($this->never())
            ->method('isInEuropeanUnion');

        $this->assertSame($result, $this->validateLocation->validate([], $countries), $case);
    }

    public function countiesProvider(): array
    {
        return [
            [
                'case'        => 'Countries <=> Another country',
                'countries'   => ['PL', 'AU'],
                'countryCode' => 'FR',
                'result'      => false,
            ],
            [
                'case'        => 'Countries <=> One of the countries',
                'countries'   => ['PL', 'AU'],
                'countryCode' => 'PL',
                'result'      => true,
            ],
        ];
    }

    /**
     * @dataProvider statesProvider
     *
     * @param string $case
     * @param array  $regions
     * @param array  $countries
     * @param array  $usaStates
     * @param        $isInEU
     * @param string $countryCode
     * @param        $countryState
     * @param bool   $result
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testStates(
        string $case,
        array $regions,
        array $countries,
        array $usaStates,
        $isInEU,
        $countryCode,
        $countryState,
        bool $result
    ) {
        $this->geoIPLookup
            ->expects($this->once())
            ->method('canUse')
            ->willReturn(true);

        $this->geoIPLookup
            ->expects($this->once())
            ->method('getCountryCode')
            ->willReturn($countryCode);

        $this->geoIPLookup
            ->method('getCurrentCountryState')
            ->willReturn($countryState);

        $this->geoIPLookup
            ->method('isInEuropeanUnion')
            ->willReturn($isInEU);

        $this->assertSame($result, $this->validateLocation->validate($regions, $countries, $usaStates), $case);
    }

    public function statesProvider(): array
    {
        return [
            [
                'case' => 'US + California <=> California',
                'regions' => [],
                'countries' => [LocationsListInterface::COUNTRY_CODE_USA],
                'usaStates' => [LocationsListInterface::USA_STATE_CALIFORNIA],
                'isInEU'  => false,
                'countryCode' => LocationsListInterface::COUNTRY_CODE_USA,
                'countryState' => LocationsListInterface::USA_STATE_CALIFORNIA,
                'result'  => true,
            ],
            [
                'case' => 'US + California <=> Another state',
                'regions' => [],
                'countries' => [LocationsListInterface::COUNTRY_CODE_USA],
                'usaStates' => [LocationsListInterface::USA_STATE_CALIFORNIA],
                'isInEU'  => false,
                'countryCode' => LocationsListInterface::COUNTRY_CODE_USA,
                'countryState' => 'Nevada',
                'result'  => false,
            ],
            [
                'case' => 'US + California <=> Another country',
                'regions' => [],
                'countries' => [LocationsListInterface::COUNTRY_CODE_USA],
                'usaStates' => [LocationsListInterface::USA_STATE_CALIFORNIA],
                'isInEU'  => false,
                'countryCode' => 'CA',
                'countryState' => null,
                'result'  => false,
            ],
            [
                'case' => 'Unknown + US + California <=> Unknown',
                'regions' => [LocationsListInterface::UNKNOWN],
                'countries' => [LocationsListInterface::COUNTRY_CODE_USA],
                'usaStates' => [LocationsListInterface::USA_STATE_CALIFORNIA],
                'isInEU'  => null,
                'countryCode' => null,
                'countryState' => null,
                'result'  => true,
            ],
        ];
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testParseOptions()
    {
        $this->geoIPLookup
            ->expects($this->exactly(3))
            ->method('canUse')
            ->willReturn(true);

        $this->geoIPLookup
            ->expects($this->exactly(3))
            ->method('getCountryCode')
            ->willReturn('CA');

        $this->geoIPLookup
            ->expects($this->exactly(3))
            ->method('isInEuropeanUnion')
            ->willReturn(false);

        $this->assertFalse($this->validateLocation->validateByMergedOptions(
            [LocationsListInterface::COUNTRY_CODE_USA, LocationsList::EU]
        ));

        $this->assertTrue($this->validateLocation->validateByMergedOptions(
            ['CA', LocationsList::EU]
        ));

        $this->assertTrue($this->validateLocation->validateByMergedOptions(
            [LocationsList::UNKNOWN, 'CA', LocationsList::EU]
        ));
    }
}
