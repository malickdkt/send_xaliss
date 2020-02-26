<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * normalizationContext={"groups"={"read"}},
 * denormalizationContext={"groups"={"write"}})
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 */
class Compte
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Groups({"write", "read"})
     */
    private $numero_compte;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"write", "read"})
     */
    private $solde;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="Users",cascade={"persist"})
     * @Groups({"write", "read"})
     */
    private $comptes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="Compte",cascade={"persist"})
     * @Groups({"write", "read"})
     */
    private $depots;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="Compte", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"write", "read"})
     */
    private $partenaire;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"write", "read"})
     */
    private $createAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comptes", cascade={"persist"})
     */
    private $user;

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
        $this->comptes = new ArrayCollection();
        $this->depots = new ArrayCollection();
        $this->createAt = new DateTime();
        $this->affectations = new ArrayCollection();
        $this->userTransaction = new ArrayCollection();
        $this->compteTransaction = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCompte(): ?string
    {
        return $this->numero_compte;
    }

    public function setNumeroCompte(string $numero_compte): self
    {
        $this->numero_compte = $numero_compte;

        return $this;
    }

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): self
    {
        $this->solde = $solde;

        return $this;
    }



    /**
     * @return Collection|self[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

   

   
    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->contains($depot)) {
            $this->depots->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

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

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

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
