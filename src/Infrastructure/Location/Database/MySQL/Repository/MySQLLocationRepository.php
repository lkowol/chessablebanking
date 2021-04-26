<?php

namespace ChessableBanking\Infrastructure\Location\Database\MySQL\Repository;

use ChessableBanking\Domain\Location\Entity\Location;
use ChessableBanking\Domain\Location\Exception\LocationValidationException;
use ChessableBanking\Domain\Location\Repository\LocationRepositoryInterface;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use ChessableBanking\Infrastructure\Location\Database\MySQL\Processor\LocationResultProcessor;
use ChessableBanking\Infrastructure\Location\Database\MySQL\Validator\LocationValidator;
use PDO;

class MySQLLocationRepository implements LocationRepositoryInterface
{

    private MySQLConnection $mySQLConnection;
    private LocationResultProcessor $locationResultProcessor;
    private LocationValidator $locationValidator;

    public function __construct(
        MySQLConnection $mySQLConnection,
        LocationResultProcessor $locationResultProcessor,
        LocationValidator $locationValidator
    ) {
        $this->mySQLConnection = $mySQLConnection;
        $this->locationResultProcessor = $locationResultProcessor;
        $this->locationValidator = $locationValidator;
    }

    public function findAll(): array
    {
        $statement = $this->mySQLConnection
            ->query("SELECT 
                `id`, `city`, `street`, `building_number`, `apartment_number`, `postal_code`, `country_id` 
            FROM `location`");
        $statement->execute();

        return $this->locationResultProcessor->processCollection((array) $statement->fetchAll(PDO::FETCH_OBJ));
    }

    public function find(string $id): ?Location
    {
        $statement = $this->mySQLConnection
            ->prepare("SELECT 
                `id`, `city`, `street`, `building_number`, `apartment_number`, `postal_code`, `country_id` 
            FROM `location` WHERE `id` = :id");
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);
        if (empty($result)) {
            return null;
        }

        return $this->locationResultProcessor->processRow($result);
    }

    /**
     * @param Location $location
     * @return Location
     * @throws LocationValidationException
     */
    public function create(Location $location): Location
    {
        $this->locationValidator->validate($location);
        $statement = $this->mySQLConnection->prepare("
            INSERT INTO `location` 
                (`id`, `city`, `street`, `building_number`, `apartment_number`, `postal_code`, `country_id`)
                VALUES
	        (:id, :city, :street, :buildingNumber, :apartmentNumber, :postalCode, :countryId);");
        $statement->bindValue(':id', $location->getId());
        $statement->bindValue(':city', $location->getCity());
        $statement->bindValue(':street', $location->getStreet());
        $statement->bindValue(':buildingNumber', $location->getBuildingNumber());
        $statement->bindValue(':apartmentNumber', $location->getApartmentNumber());
        $statement->bindValue(':postalCode', $location->getPostalCode());
        $statement->bindValue(':countryId', $location->getCountry()->getId());

        $statement->execute();

        return $location;
    }
}
