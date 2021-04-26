<?php

namespace ChessableBanking\Domain\Branch\Entity;

use ChessableBanking\Domain\Location\Entity\Location;

class Branch
{

    private string $id;
    private string $name;

    /*
     * According to my research, bank branch is physical location, but anyway:
     * TODO: Consider nullability, some countries or concepts may allow fully online branches without location
     */
    private Location $location;

    public function __construct(string $id, string $name, Location $location)
    {
        $this->id = $id;
        $this->name = $name;
        $this->location = $location;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
