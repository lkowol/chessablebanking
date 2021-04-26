<?php

namespace ChessableBanking\Infrastructure\Location\Database\MySQL\Processor;

use ChessableBanking\Domain\Country\Repository\CountryRepositoryInterface;
use ChessableBanking\Domain\Location\Entity\Location;
use ChessableBanking\Domain\Location\Exception\LocationValidationException;
use ChessableBanking\Domain\Location\Validator\LocationValidationError;
use ChessableBanking\Infrastructure\Location\Database\MySQL\Builder\LocationEntityBuilder;
use ChessableBanking\Infrastructure\Location\Database\MySQL\Validator\LocationValidator;
use Psr\Log\LoggerInterface;
use stdClass;

class LocationResultProcessor
{

    private LocationEntityBuilder $locationEntityBuilder;
    private LocationValidator $locationValidator;
    private LoggerInterface $logger;
    private CountryRepositoryInterface $countryRepository;

    public function __construct(
        LocationEntityBuilder $locationEntityBuilder,
        LocationValidator $locationValidator,
        LoggerInterface $logger,
        CountryRepositoryInterface $countryRepository
    ) {
        $this->locationEntityBuilder = $locationEntityBuilder;
        $this->locationValidator = $locationValidator;
        $this->logger = $logger;
        $this->countryRepository = $countryRepository;
    }

    public function processRow(stdClass $location): ?Location
    {
        $country = $this->countryRepository->find($location->country_id ?? null);
        if (null === $country) {
            $this->logger->error(sprintf('Unable to provide country for location with ID %s', $location->id ?? ''));
            return null;
        }

        $locationEntity = $this->locationEntityBuilder->build($location, $country);
        try {
            $this->locationValidator->validate($locationEntity);
        } catch (LocationValidationException $e) {
            $this->logger->error(
                sprintf(
                    'Unable to build correct location from database data for location with ID %s due to: %s',
                    $location->id ?? '',
                    join(
                        ', ',
                        array_map(fn(LocationValidationError $error) => $error->getErrorMessage(), $e->getErrors())
                    )
                )
            );
            return null;
        }

        return $locationEntity;
    }

    /**
     * @param array|stdClass[] $locations
     * @return Location[]
     */
    public function processCollection(array $locations): array
    {
        $locationEntities = [];
        foreach ($locations as $location) {
            $locationEntity = $this->processRow($location);
            if (null === $locationEntity) {
                // Just skip & log this, something was tried to be converted, but definitely not the correct entity
                continue;
            }

            $locationEntities[] = $locationEntity;
        }

        return $locationEntities;
    }
}
