<?php

namespace ChessableBanking\Application\Branch\Model;

class BranchWithLocation
{

    private string $name;
    private string $city;
    private string $street;
    private string $buildingNumber;
    private ?string $apartmentNumber;
    private string $postalCode;
    private string $countryId;

    public function __construct(
        string $name,
        string $city,
        string $street,
        string $buildingNumber,
        ?string $apartmentNumber,
        string $postalCode,
        string $countryId
    ) {
        $this->name = $name;
        $this->city = $city;
        $this->street = $street;
        $this->buildingNumber = $buildingNumber;
        $this->apartmentNumber = $apartmentNumber;
        $this->postalCode = $postalCode;
        $this->countryId = $countryId;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getCountryId(): string
    {
        return $this->countryId;
    }
}
