<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EspecieRepository")
 */
class Especie
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=80)
     */
    private $nome_cientifico;

    /**
     * @ORM\Column(type="text")
     */
    private $caracteristicas;

    public function getNomeCientifico(): ?string
    {
        return $this->nome_cientifico;
    }

    public function setNomeCientifico(string $nome_cientifico): self
    {
        $this->nome_cientifico = $nome_cientifico;

        return $this;
    }

    public function getCaracteristicas(): ?string
    {
        return $this->caracteristicas;
    }

    public function setCaracteristicas(string $caracteristicas): self
    {
        $this->caracteristicas = $caracteristicas;

        return $this;
    }
}
