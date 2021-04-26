<?php

namespace ChessableBanking\Domain\Country\Entity;

class Country
{

    private string $id;
    private string $name;
    private string $iso3166Alpha3Code;

    public function __construct(string $id, string $name, string $iso3166Alpha3Code)
    {
        $this->id = $id;
        $this->name = $name;
        $this->iso3166Alpha3Code = $iso3166Alpha3Code;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIso3166Alpha3Code(): string
    {
        return $this->iso3166Alpha3Code;
    }
}
