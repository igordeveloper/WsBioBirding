<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BirdListRepository")
 */
class BirdList
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
    private $rg;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Species")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="scientific_name", name="scientific_name")
     */
    private $scientificName;

    /**
    * @ORM\Column(type="string", nullable=false, length=7)
     */ 
    private $age;

    /**
     * @ORM\Column(type="string", nullable=false, length=13)
     */
    private $sex;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=7)
     */
    private $latitud;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $details;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $idCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRg(): ?User
    {
        return $this->rg;
    }

    public function setRg(?User $rg): self
    {
        $this->rg = $rg;

        return $this;
    }

    public function getScientificName(): ?Species
    {
        return $this->scientificName;
    }

    public function setScientificName(?Species $scientificName): self
    {
        $this->scientificName = $scientificName;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(string $age): self
    {
        if (!in_array($status, array(self::STATUS_VISIBLE, self::STATUS_INVISIBLE))) {
            throw new \InvalidArgumentException("Invalid age");
        }
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

    public function getLatitud(): ?float
    {
        return $this->latitud;
    }

    public function setLatitud(float $latitud): self
    {
        $this->latitud = $latitud;

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

    public function getHumidity(): ?int
    {
        return $this->humidity;
    }

    public function setHumidity(int $humidity): self
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

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

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

    public function getIdCode(): ?string
    {
        return $this->idCode;
    }

    public function setIdCode(string $idCode): self
    {
        $this->idCode = $idCode;

        return $this;
    }
}
