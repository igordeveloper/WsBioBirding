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
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120, unique=true)
     */
    private $scientificName;

    /**
     * @ORM\Column(type="text", nullable = true)
     */
    private $notes;

    /**
     * @ORM\Column(type="string", length=20, nullable = true)
     */
    private $conservationState;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    public function setScientificName(string $scientificName): self
    {
        $this->scientificName = $scientificName;

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

    public function getConservationState(): ?string
    {
        return $this->conservationState;
    }

    public function setConservationState(?string $conservationState): self
    {
        $this->conservationState = $conservationState;

        return $this;
    }

}
