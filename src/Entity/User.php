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
    private $accessLevel;

    /**
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private $enabled;


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
        return $this->accessLevel;
    }

    public function setAccessLevel(?AccessLevel $accessLevel): self
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }


}
