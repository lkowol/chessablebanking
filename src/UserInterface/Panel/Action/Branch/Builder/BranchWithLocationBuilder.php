<?php

namespace ChessableBanking\UserInterface\Panel\Action\Branch\Builder;

use ChessableBanking\Application\Branch\Model\BranchWithLocation;

class BranchWithLocationBuilder
{

    public function build(array $formData): BranchWithLocation
    {
        return new BranchWithLocation(
            $formData['name'] ?? '',
            $formData['city'] ?? '',
            $formData['street'] ?? '',
            $formData['buildingNumber'] ?? '',
            $formData['apartmentNumber'] ?? null,
            $formData['postalCode'] ?? '',
            $formData['country'] ?? ''
        );
    }
}
