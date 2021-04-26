<?php

namespace ChessableBanking\Infrastructure\Location\Database\MySQL\Validator;

use ChessableBanking\Domain\Location\Entity\Location;
use ChessableBanking\Domain\Location\Exception\LocationValidationException;
use ChessableBanking\Domain\Location\Validator\LocationValidationError;

class LocationValidator
{

    /**
     * Seems redundant, but not really - validates only MySQL contract, skips business
     * @throws LocationValidationException
     */
    public function validate(Location $location): void
    {
        $errors = [];

        $locationCityLength = strlen($location->getCity());
        if ($locationCityLength > 255) {
            $errors[] = new LocationValidationError('name', 'City must not be longer than 255 characters');
        }

        if (strlen($location->getBuildingNumber()) > 32) {
            $errors[] = new LocationValidationError(
                'buildingNumber',
                'Building number must not be longer than 32 characters'
            );
        }

        if (strlen((string) $location->getApartmentNumber()) > 32) {
            $errors[] = new LocationValidationError(
                'apartmentNumber',
                'Apartment number must not be longer than 32 characters'
            );
        }

        if (strlen($location->getPostalCode()) > 8) {
            $errors[] = new LocationValidationError('postalCode', 'Postal code must be shorter than 8 characters long');
        }

        if (strlen($location->getStreet()) > 255) {
            $errors[] = new LocationValidationError('street', 'Street must not be longer than 32 characters');
        }

        if (!empty($errors)) {
            throw new LocationValidationException($errors);
        }
    }
}
