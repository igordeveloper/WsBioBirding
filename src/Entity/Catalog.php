<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CatalogRepository")
 */
class Catalog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="rg", name="rg")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Species")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="scientific_name", name="scientific_name")
     */
    private $species;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=13)
     */
    private $sex;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=7)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=7)
     */
    private $longitude;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $temperature;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $humidity;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $wind;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $weather;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $identificationCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSpecies(): ?Species
    {
        return $this->species;
    }

    public function setSpecies(?Species $species): self
    {
        $this->species = $species;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getHumidity(): ?float
    {
        return $this->humidity;
    }

    public function setHumidity(float $humidity): self
    {
        $this->humidity = $humidity;

        return $this;
    }

    public function getWind(): ?float
    {
        return $this->wind;
    }

    public function setWind(float $wind): self
    {
        $this->wind = $wind;

        return $this;
    }

    public function getWeather(): ?string
    {
        return $this->weather;
    }

    public function setWeather(string $weather): self
    {
        $this->weather = $weather;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIdentificationCode(): ?string
    {
        return $this->identificationCode;
    }

    public function setIdentificationCode(?string $identificationCode): self
    {
        $this->identificationCode = $identificationCode;

        return $this;
    }
}
