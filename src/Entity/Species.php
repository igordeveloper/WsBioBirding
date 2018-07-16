<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private $scientific_name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $characteristics;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PopularName", mappedBy="scientific_name")
     */
    private $popularNames;

    public function __construct()
    {
        $this->popularNames = new ArrayCollection();
    }

    public function getScientificName(): ?string
    {
        return $this->scientific_name;
    }

    public function setScientificName(string $scientific_name): self
    {
        $this->scientific_name = $scientific_name;

        return $this;
    }

    public function getCharacteristics(): ?string
    {
        return $this->characteristics;
    }

    public function setCharacteristics(?string $characteristics): self
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    /**
     * @return Collection|PopularName[]
     */
    public function getPopularNames(): Collection
    {
        return $this->popularNames;
    }

    public function addPopularName(PopularName $popularName): self
    {
        if (!$this->popularNames->contains($popularName)) {
            $this->popularNames[] = $popularName;
            $popularName->setScientificName($this);
        }

        return $this;
    }

    public function removePopularName(PopularName $popularName): self
    {
        if ($this->popularNames->contains($popularName)) {
            $this->popularNames->removeElement($popularName);
            // set the owning side to null (unless already changed)
            if ($popularName->getScientificName() === $this) {
                $popularName->setScientificName(null);
            }
        }

        return $this;
    }
}
