<?php

namespace ChessableBanking\Infrastructure\Branch\Database\MySQL\Builder;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Location\Entity\Location;
use stdClass;

class BranchEntityBuilder
{

    public function build(stdClass $branch, Location $location): Branch
    {
        return new Branch(
            $branch->id ?? '',
            $branch->name ?? '',
            $location,
        );
    }
}
