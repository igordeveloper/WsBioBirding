<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=13)
     */
    private $rg;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=12, unique=true)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=15, unique=true, nullable=true, unique=true)
     */
    private $crBio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AccessLevel")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="access_level", name="access_level")
     */
    private $access_level;

    public function getRg(): ?string
    {
        return $this->rg;
    }

    public function setRg(string $rg): self
    {
        $this->rg = $rg;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getCrBio(): ?string
    {
        return $this->crBio;
    }

    public function setCrBio(?string $crBio): self
    {
        $this->crBio = $crBio;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAccessLevel(): ?AccessLevel
    {
        return $this->access_level;
    }

    public function setAccessLevel(?AccessLevel $access_level): self
    {
        $this->access_level = $access_level;

        return $this;
    }


}
