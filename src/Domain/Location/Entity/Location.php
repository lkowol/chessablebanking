<?php

namespace ChessableBanking\Domain\Location\Entity;

use ChessableBanking\Domain\Country\Entity\Country;

class Location
{

    private string $id;

    /*
     * TODO: Consider city to behave like country (to be entity, selectable [but with autocomplete in GUI])
     * Businessly it may be the same. Maybe even the street?
     */
    private string $city;
    private string $street;
    private string $buildingNumber;
    private ?string $apartmentNumber;
    private string $postalCode;
    private Country $country;

    public function __construct(
        string $id,
        string $city,
        string $street,
        string $buildingNumber,
        ?string $apartmentNumber,
        string $postalCode,
        Country $country
    ) {
        $this->id = $id;
        $this->city = $city;
        $this->street = $street;
        $this->buildingNumber = $buildingNumber;
        $this->apartmentNumber = $apartmentNumber;
        $this->postalCode = $postalCode;
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getBuildingNumber(): string
    {
        return $this->buildingNumber;
    }

    public function getApartmentNumber(): ?string
    {
        return $this->apartmentNumber;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }
}
