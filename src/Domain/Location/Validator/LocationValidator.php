<?php

namespace ChessableBanking\Domain\Location\Validator;

use ChessableBanking\Domain\Country\Repository\CountryRepositoryInterface;
use ChessableBanking\Domain\Location\Entity\Location;
use ChessableBanking\Domain\Location\Exception\LocationValidationException;
use ChessableBanking\Domain\Location\Repository\LocationRepositoryInterface;

class LocationValidator
{

    private LocationRepositoryInterface $locationRepository;
    private CountryRepositoryInterface $countryRepository;

    public function __construct(
        LocationRepositoryInterface $locationRepository,
        CountryRepositoryInterface $countryRepository
    ) {
        $this->locationRepository = $locationRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param Location $location
     * @param bool $validateForCreation
     * @throws LocationValidationException
     */
    public function validate(Location $location, bool $validateForCreation = false): void
    {
        $errors = [];
        if (empty($location->getId())) {
            $errors[] = new LocationValidationError('id', 'Id must not be empty');
        }

        $locationCityLength = strlen($location->getCity());
        if ($locationCityLength < 3 || $locationCityLength > 255) {
            $errors[] = new LocationValidationError('name', 'City must be between 3 and 255 characters long');
        }

        if (empty($location->getBuildingNumber()) || strlen($location->getBuildingNumber()) > 32) {
            $errors[] = new LocationValidationError(
                'buildingNumber',
                'Building number must be between 1 and 32 characters long'
            );
        }

        if (strlen((string)$location->getApartmentNumber()) > 32) {
            $errors[] = new LocationValidationError(
                'apartmentNumber',
                'Apartment number must be less than 32 characters long'
            );
        }

        // TODO: Country based postal code validator, validating formats using regexp
        if (strlen($location->getPostalCode()) > 8) {
            $errors[] = new LocationValidationError('postalCode', 'Postal code must be shorter than 8 characters long');
        }

        if (empty($location->getStreet()) || strlen($location->getStreet()) > 255) {
            $errors[] = new LocationValidationError('street', 'Street must be between 1 and 255 characters long');
        }

        if (null === $this->countryRepository->find($location->getCountry()->getId())) {
            $errors[] = new LocationValidationError('country', 'Not existing country given');
        }

        if ($validateForCreation && null !== $this->locationRepository->find($location->getId())) {
            $errors[] = new LocationValidationError('id', 'Id must be unique');
        }

        if (!empty($errors)) {
            throw new LocationValidationException($errors);
        }
    }
}
