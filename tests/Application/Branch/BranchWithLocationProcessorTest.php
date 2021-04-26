<?php

namespace ChessableBanking\Tests\Application\Branch;

use ChessableBanking\Application\Branch\BranchWithLocationProcessor;
use ChessableBanking\Application\Branch\Model\BranchWithLocation;
use ChessableBanking\Application\Security\TransactionInsurerInterface;
use ChessableBanking\Domain\Branch\BranchService;
use ChessableBanking\Domain\Country\Entity\Country;
use ChessableBanking\Domain\Country\Repository\CountryRepositoryInterface;
use ChessableBanking\Domain\Location\Entity\Location;
use ChessableBanking\Domain\Location\Exception\LocationValidationException;
use ChessableBanking\Domain\Location\LocationService;
use ChessableBanking\Domain\Location\Validator\LocationValidationError;
use PHPUnit\Framework\TestCase;

class BranchWithLocationProcessorTest extends TestCase
{

    public function testProcessorSuccessfulProcess(): void
    {
        $branchWithLocation = $this->createMock(BranchWithLocation::class);
        $branchWithLocation->expects($this->once())->method('getCity')->willReturn('theCity');
        $branchWithLocation->expects($this->once())->method('getStreet')->willReturn('theStreet');
        $branchWithLocation->expects($this->once())->method('getBuildingNumber')->willReturn('theBuildingNumber');
        $branchWithLocation->expects($this->once())->method('getApartmentNumber')->willReturn('theApartmentNumber');
        $branchWithLocation->expects($this->once())->method('getPostalCode')->willReturn('thePostalCode');
        $branchWithLocation->expects($this->once())->method('getName')->willReturn('theName');
        $branchWithLocation->expects($this->once())->method('getCountryId')->willReturn('theCountryId');

        $country = $this->createMock(Country::class);
        $location = $this->createMock(Location::class);

        $branchService = $this->createMock(BranchService::class);
        $branchService->expects($this->once())->method('create')->with(
            $this->anything(),
            'theName',
            $location
        );

        $locationService = $this->createMock(LocationService::class);
        $locationService->expects($this->once())->method('create')->with(
            $this->anything(),
            'theCity',
            'theStreet',
            'theBuildingNumber',
            'theApartmentNumber',
            'thePostalCode',
            $country
        )->willReturn($location);

        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $countryRepository->expects($this->once())->method('find')->with('theCountryId')->willReturn($country);

        $transactionInsurer = $this->createMock(TransactionInsurerInterface::class);
        $transactionInsurer->expects($this->once())->method('beginTransaction');
        $transactionInsurer->expects($this->once())->method('commit');

        $processor = new BranchWithLocationProcessor(
            $branchService,
            $locationService,
            $countryRepository,
            $transactionInsurer
        );

        $this->assertEmpty($processor->create($branchWithLocation));
    }

    public function testProcessorFailedProcess(): void
    {
        $branchWithLocation = $this->createMock(BranchWithLocation::class);

        $branchService = $this->createMock(BranchService::class);
        $locationService = $this->createMock(LocationService::class);
        $locationService->expects($this->once())->method('create')->willThrowException(
            new LocationValidationException([
                new LocationValidationError('theField', 'theMessage')
            ])
        );

        $countryRepository = $this->createMock(CountryRepositoryInterface::class);
        $transactionInsurer = $this->createMock(TransactionInsurerInterface::class);
        $transactionInsurer->expects($this->once())->method('beginTransaction');
        $transactionInsurer->expects($this->once())->method('rollback');

        $processor = new BranchWithLocationProcessor(
            $branchService,
            $locationService,
            $countryRepository,
            $transactionInsurer
        );

        $result = $processor->create($branchWithLocation);

        $this->assertCount(1, $result);
        $this->assertEquals('theMessage', $result['theField']);
    }
}
