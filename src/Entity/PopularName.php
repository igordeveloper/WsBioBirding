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
     * @ORM\JoinColumn(nullable=false, referencedColumnName="scientific_name", name="scientific_name")
     */
    private $scientificName;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
}
