<?php

namespace ChessableBanking\Domain\Branch\Validator;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Branch\Exception\BranchValidationException;
use ChessableBanking\Domain\Branch\Repository\BranchRepositoryInterface;

/*
 * Not using Symfony validators as it would cause leak of business knowledge out of domain
 */
class BranchValidator
{

    private BranchRepositoryInterface $branchRepository;

    public function __construct(BranchRepositoryInterface $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    /**
     * @param Branch $branch
     * @param bool $validateForCreation
     * @throws BranchValidationException
     */
    public function validate(Branch $branch, bool $validateForCreation = false): void
    {
        $errors = [];

        if (empty($branch->getId())) {
            $errors[] = new BranchValidationError('id', 'Id must not be empty');
        }

        $branchNameLength = strlen($branch->getName());
        if ($branchNameLength < 3 || $branchNameLength > 255) {
            $errors[] = new BranchValidationError(
                'name',
                'Name must be between 3 and 255 characters long'
            );
        }

        if ($validateForCreation && null !== $this->branchRepository->find($branch->getId())) {
            $errors[] = new BranchValidationError('id', 'Id must be unique');
        }

        if ($validateForCreation && null !== $this->branchRepository->findByName($branch->getName())) {
            $errors[] = new BranchValidationError('name', 'Name must be unique');
        }

        if (!empty($errors)) {
            throw new BranchValidationException($errors);
        }
    }
}
