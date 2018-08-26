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
    private $notes;

    /**
     * @ORM\Column(type="string", length=20, nullable = true)
     */
    private $conservationState;

    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): self
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
