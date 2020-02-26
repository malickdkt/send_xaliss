<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomE;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomE;

    /**
     * @ORM\Column(type="integer")
     */
    private $telephoneE;

    /**
     * @ORM\Column(type="integer")
     */
    private $npieceE;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomB;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomB;

    /**
     * @ORM\Column(type="integer")
     */
    private $telephoneB;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $npieceB;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="float")
     */
    private $frais;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnvoi;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="float")
     */
    private $comEtat;

    /**
     * @ORM\Column(type="float")
     */
    private $comSysteme;

    /**
     * @ORM\Column(type="float")
     */
    private $comEnvoi;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $comRetrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="userTransaction")
     */
    private $envoi;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="compteTransaction")
     */
    private $retrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactionUserEnvoi")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userEnvoi;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactionUserRetrait")
     */
    private $userRetrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $code;

    public function __construct()
    {
        $this->etat = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenomE(): ?string
    {
        return $this->prenomE;
    }

    public function setPrenomE(string $prenomE): self
    {
        $this->prenomE = $prenomE;

        return $this;
    }

    public function getNomE(): ?string
    {
        return $this->nomE;
    }

    public function setNomE(string $nomE): self
    {
        $this->nomE = $nomE;

        return $this;
    }

    public function getTelephoneE(): ?int
    {
        return $this->telephoneE;
    }

    public function setTelephoneE(int $telephoneE): self
    {
        $this->telephoneE = $telephoneE;

        return $this;
    }

    public function getNpieceE(): ?int
    {
        return $this->npieceE;
    }

    public function setNpieceE(int $npieceE): self
    {
        $this->npieceE = $npieceE;

        return $this;
    }

    public function getPrenomB(): ?string
    {
        return $this->prenomB;
    }

    public function setPrenomB(string $prenomB): self
    {
        $this->prenomB = $prenomB;

        return $this;
    }

    public function getNomB(): ?string
    {
        return $this->nomB;
    }

    public function setNomB(string $nomB): self
    {
        $this->nomB = $nomB;

        return $this;
    }

    public function getTelephoneB(): ?int
    {
        return $this->telephoneB;
    }

    public function setTelephoneB(int $telephoneB): self
    {
        $this->telephoneB = $telephoneB;

        return $this;
    }

    public function getNpieceB(): ?int
    {
        return $this->npieceB;
    }

    public function setNpieceB(?int $npieceB): self
    {
        $this->npieceB = $npieceB;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getFrais(): ?float
    {
        return $this->frais;
    }

    public function setFrais(float $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(?\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getComEtat(): ?float
    {
        return $this->comEtat;
    }

    public function setComEtat(float $comEtat): self
    {
        $this->comEtat = $comEtat;

        return $this;
    }

    public function getComSysteme(): ?float
    {
        return $this->comSysteme;
    }

    public function setComSysteme(float $comSysteme): self
    {
        $this->comSysteme = $comSysteme;

        return $this;
    }

    public function getComEnvoi(): ?float
    {
        return $this->comEnvoi;
    }

    public function setComEnvoi(float $comEnvoi): self
    {
        $this->comEnvoi = $comEnvoi;

        return $this;
    }

    public function getComRetrait(): ?float
    {
        return $this->comRetrait;
    }

    public function setComRetrait(?float $comRetrait): self
    {
        $this->comRetrait = $comRetrait;

        return $this;
    }

    public function getEnvoi(): ?Compte
    {
        return $this->envoi;
    }

    public function setEnvoi(?Compte $envoi): self
    {
        $this->envoi = $envoi;

        return $this;
    }

    public function getRetrait(): ?Compte
    {
        return $this->retrait;
    }

    public function setRetrait(?Compte $retrait): self
    {
        $this->retrait = $retrait;

        return $this;
    }

    public function getUserEnvoi(): ?User
    {
        return $this->userEnvoi;
    }

    public function setUserEnvoi(?User $userEnvoi): self
    {
        $this->userEnvoi = $userEnvoi;

        return $this;
    }

    public function getUserRetrait(): ?User
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }
}
