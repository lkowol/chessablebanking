<?php

namespace ChessableBanking\Domain\Location;

use ChessableBanking\Domain\Country\Entity\Country;
use ChessableBanking\Domain\Location\Entity\Location;
use ChessableBanking\Domain\Location\Exception\LocationValidationException;
use ChessableBanking\Domain\Location\Repository\LocationRepositoryInterface;
use ChessableBanking\Domain\Location\Validator\LocationValidator;

class LocationService
{

    private LocationRepositoryInterface $locationRepository;
    private LocationValidator $locationValidator;

    public function __construct(LocationRepositoryInterface $locationRepository, LocationValidator $locationValidator)
    {
        $this->locationRepository = $locationRepository;
        $this->locationValidator = $locationValidator;
    }

    /**
     * @param string $id
     * @param string $city
     * @param string $street
     * @param string $buildingNumber
     * @param string|null $apartmentNumber
     * @param string $postalCode
     * @param Country $country
     * @return Location
     * @throws LocationValidationException
     */
    public function create(
        string $id,
        string $city,
        string $street,
        string $buildingNumber,
        ?string $apartmentNumber,
        string $postalCode,
        Country $country
    ): Location {
        $location = new Location($id, $city, $street, $buildingNumber, $apartmentNumber, $postalCode, $country);
        $this->locationValidator->validate($location, true);
        return $this->locationRepository->create($location);
    }

    /**
     * @return Location[]
     */
    public function provideAll(): array
    {
        return $this->locationRepository->findAll();
    }
}
