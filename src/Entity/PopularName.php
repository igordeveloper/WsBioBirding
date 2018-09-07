<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PopularNameRepository")
 */
class PopularName
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=120)
     */
    private $name;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Species")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id", name="species")
     */
    private $species;

    public function getName(): ?string
    {
        return $this->name;
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

}
