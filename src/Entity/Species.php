<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpeciesRepository")
 */
class Species
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=120)
     */
    private $scientificName;

    /**
     * @ORM\Column(type="text")
     */
    private $characteristics;

    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    public function setScientificName(string $scientificName): self
    {
        $this->scientificName = $scientificName;

        return $this;
    }

    public function getCharacteristics(): ?string
    {
        return $this->characteristics;
    }

    public function setCharacteristics(string $characteristics): self
    {
        $this->characteristics = $characteristics;

        return $this;
    }
}
