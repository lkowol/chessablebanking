<?php


namespace ChessableBanking\Application\Branch;

use ChessableBanking\Application\Branch\Model\BranchWithLocation;
use ChessableBanking\Application\Security\TransactionInsurerInterface;
use ChessableBanking\Domain\Branch\BranchService;
use ChessableBanking\Domain\Branch\Exception\BranchValidationException;
use ChessableBanking\Domain\Branch\Validator\BranchValidationError;
use ChessableBanking\Domain\Country\Entity\Country;
use ChessableBanking\Domain\Country\Repository\CountryRepositoryInterface;
use ChessableBanking\Domain\Location\Exception\LocationValidationException;
use ChessableBanking\Domain\Location\LocationService;
use ChessableBanking\Domain\Location\Validator\LocationValidationError;
use Symfony\Component\Uid\Uuid;

class BranchWithLocationProcessor
{

    private BranchService $branchService;
    private LocationService $locationService;
    private CountryRepositoryInterface $countryRepository;
    private TransactionInsurerInterface $transactionInsurer;

    public function __construct(
        BranchService $branchService,
        LocationService $locationService,
        CountryRepositoryInterface $countryRepository,
        TransactionInsurerInterface $transactionInsurer
    ) {
        $this->branchService = $branchService;
        $this->locationService = $locationService;
        $this->countryRepository = $countryRepository;
        $this->transactionInsurer = $transactionInsurer;
    }

    /**
     * @param BranchWithLocation $branchWithLocation
     * @return array<string,string>
     */
    public function create(BranchWithLocation $branchWithLocation): array
    {
        try {
            $this->transactionInsurer->beginTransaction();
            $location = $this->locationService->create(
                Uuid::v6(),
                $branchWithLocation->getCity(),
                $branchWithLocation->getStreet(),
                $branchWithLocation->getBuildingNumber(),
                $branchWithLocation->getApartmentNumber(),
                $branchWithLocation->getPostalCode(),
                $this->countryRepository->find($branchWithLocation->getCountryId()) ?? new Country('', '', '')
            );

            $this->branchService->create(
                Uuid::v6(),
                $branchWithLocation->getName(),
                $location
            );
            $this->transactionInsurer->commit();

            return [];
        } catch (LocationValidationException|BranchValidationException $e) {
            $this->transactionInsurer->rollback();
            return array_combine(
                array_map(fn(LocationValidationError|BranchValidationError $e) => $e->getFieldName(), $e->getErrors()),
                array_map(fn(LocationValidationError|BranchValidationError $e) => $e->getErrorMessage(), $e->getErrors())
            );
        }
    }
}
