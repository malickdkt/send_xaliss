<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * normalizationContext={"groups"={"compte"}})
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 * @UniqueEntity("numCompte" , message="ce numéro de compte  existe déja.")
 */
class Compte
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
    * @Groups({"compte"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
    * @Groups({"compte"})
     */
    private $numCompte;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
     * @Groups({"compte"})
     */
    private $solde;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
     * @Groups({"compte"})
     * )
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comptes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
     * @Groups({"compte"})
     */
    private $userCreateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="comptes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
     * @Groups({"compte"})
     */
    private $partenaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="compte")
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
     * @Groups({"compte"})
     */
    private $depot;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Affectation", mappedBy="compte")
     */
    private $affectations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="depot")
     */
    private $userTransaction;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="retrait")
     */
    private $compteTransaction;

    public function __construct()
    {
        $this->depot = new ArrayCollection();
        $this->affectations = new ArrayCollection();
        $this->userTransaction = new ArrayCollection();
        $this->compteTransaction = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?string
    {
        return $this->numCompte;
    }

    public function setNumCompte(string $numCompte): self
    {
        $this->numCompte = $numCompte;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserCreateur(): ?User
    {
        return $this->userCreateur;
    }

    public function setUserCreateur(?User $userCreateur): self
    {
        $this->userCreateur = $userCreateur;

        return $this;
    }

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(?Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepot(): Collection
    {
        return $this->depot;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depot->contains($depot)) {
            $this->depot[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depot->contains($depot)) {
            $this->depot->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return "";
    }

    /**
     * @return Collection|Affectation[]
     */
    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): self
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations[] = $affectation;
            $affectation->setCompte($this);
        }

        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if ($this->affectations->contains($affectation)) {
            $this->affectations->removeElement($affectation);
            // set the owning side to null (unless already changed)
            if ($affectation->getCompte() === $this) {
                $affectation->setCompte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getUserTransaction(): Collection
    {
        return $this->userTransaction;
    }

    public function addUserTransaction(Transaction $userTransaction): self
    {
        if (!$this->userTransaction->contains($userTransaction)) {
            $this->userTransaction[] = $userTransaction;
            $userTransaction->setEnvoi($this);
        }

        return $this;
    }

    public function removeUserTransaction(Transaction $userTransaction): self
    {
        if ($this->userTransaction->contains($userTransaction)) {
            $this->userTransaction->removeElement($userTransaction);
            // set the owning side to null (unless already changed)
            if ($userTransaction->getEnvoi() === $this) {
                $userTransaction->setEnvoi(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getCompteTransaction(): Collection
    {
        return $this->compteTransaction;
    }

    public function addCompteTransaction(Transaction $compteTransaction): self
    {
        if (!$this->compteTransaction->contains($compteTransaction)) {
            $this->compteTransaction[] = $compteTransaction;
            $compteTransaction->setRetrait($this);
        }

        return $this;
    }

    public function removeCompteTransaction(Transaction $compteTransaction): self
    {
        if ($this->compteTransaction->contains($compteTransaction)) {
            $this->compteTransaction->removeElement($compteTransaction);
            // set the owning side to null (unless already changed)
            if ($compteTransaction->getRetrait() === $this) {
                $compteTransaction->setRetrait(null);
            }
        }

        return $this;
    }
}
