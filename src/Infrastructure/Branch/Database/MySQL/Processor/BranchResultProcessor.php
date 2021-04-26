<?php

namespace ChessableBanking\Infrastructure\Branch\Database\MySQL\Processor;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Branch\Exception\BranchValidationException;
use ChessableBanking\Domain\Branch\Validator\BranchValidationError;
use ChessableBanking\Domain\Location\Repository\LocationRepositoryInterface;
use ChessableBanking\Infrastructure\Branch\Database\MySQL\Builder\BranchEntityBuilder;
use ChessableBanking\Infrastructure\Branch\Database\MySQL\Validator\BranchValidator;
use Psr\Log\LoggerInterface;
use stdClass;

class BranchResultProcessor
{

    private BranchEntityBuilder $branchEntityBuilder;
    private BranchValidator $branchValidator;
    private LoggerInterface $logger;
    private LocationRepositoryInterface $locationRepository;

    public function __construct(
        BranchEntityBuilder $branchEntityBuilder,
        BranchValidator $branchValidator,
        LoggerInterface $logger,
        LocationRepositoryInterface $locationRepository
    ) {
        $this->branchEntityBuilder = $branchEntityBuilder;
        $this->branchValidator = $branchValidator;
        $this->logger = $logger;
        $this->locationRepository = $locationRepository;
    }


    public function processRow(stdClass $branch): ?Branch
    {
        $location = $this->locationRepository->find($branch->location_id ?? null);
        if (null === $location) {
            $this->logger->error(sprintf('Unable to provide location for branch with ID %s', $branch->id ?? ''));
            return null;
        }

        $branchEntity = $this->branchEntityBuilder->build($branch, $location);
        try {
            $this->branchValidator->validate($branchEntity);
        } catch (BranchValidationException $e) {
            $this->logger->error(
                sprintf(
                    'Unable to build correct branch from database data for branch with ID %s due to: %s',
                    $branch->id ?? '',
                    join(
                        ', ',
                        array_map(fn(BranchValidationError $error) => $error->getErrorMessage(), $e->getErrors())
                    )
                )
            );
            return null;
        }

        return $branchEntity;
    }

    /**
     * @param array|stdClass[] $branches
     * @return Branch[]
     */
    public function processCollection(array $branches): array
    {
        $branchEntities = [];
        foreach ($branches as $branch) {
            $branchEntity = $this->processRow($branch);
            if (null === $branchEntity) {
                // Just skip & log this, something was tried to be converted, but definitely not the correct entity
                continue;
            }

            $branchEntities[] = $branchEntity;
        }

        return $branchEntities;
    }
}
