<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\DepotRepository")
 */
class Depot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"write", "read"})
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"write", "read"})
     */
    private $CreateAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="depots", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"write", "read"})
     * cascade={"persist"}
     */
    private $Compte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="Depot", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"write", "read"})
     * cascade={"persist"}
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->CreateAt;
    }

    public function setCreateAt(\DateTimeInterface $CreateAt): self
    {
        $this->CreateAt = $CreateAt;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->Compte;
    }

    public function setCompte(?Compte $Compte): self
    {
        $this->Compte = $Compte;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
