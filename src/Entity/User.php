<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;


/**
 * @ApiResource(
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"user:read", "user:item:get"}},
 *          },
 *          "put"={
 *              "access_control"="is_granted('POST_EDIT', object)",
 *              "access_control_message"="Accés non autorisé"
 *          },
 *          "delete"={"access_control"="is_granted('POST_EDIT',object)"}
 *     },
 *     collectionOperations={
 *          "get"={"access_control"="is_granted('ROLE_ADMIN')"},
 *          "post"={"access_control"="is_granted('POST_EDIT',object)"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * 
 * @ApiResource(iri="http://schema.org/User")
 *  @UniqueEntity("email" , message="cette adresse email existe déja.")
 */
class User implements AdvancedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
     * @Groups({"write", "read"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
     * @Groups({"write", "read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message = "Veuillez saisir une adresse email valide ." )
     * @Groups({"write", "read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
     * @Groups({"write", "read"})
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"write", "read"})
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message = "Veuillez remplir ce champ")
     * @Groups({"write", "read"})
     */
    private $role;

    

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="user", cascade={"persist"})
     * @Groups({"write", "read"})
     */
    private $Depot;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="users", cascade={"persist"})
     */
    private $partenaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="user", cascade={"persist"})
     */
    private $comptes;

   
    
   

    public function __construct()
    {
        $this->isActive = true;
        $this->Depot = new ArrayCollection();
        $this->comptes = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }
    public function getUsername(): string
    {
        return (string) $this->email;
    }
    public function getRoles(): array
    {
        
        return [strtoupper($this->role->getLibelle())];
    }
    
    public function getSalt()
    {
        return null;

    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        return null;
    }

    public function isAccountNonExpired(){
        return true;
    }
    public function isAccountNonLocked(){
        return true;
    }
    public function isCredentialsNonExpired(){
        return true;
    }
    public function isEnabled(){
        return $this->getIsActive();
    }

   

    /**
     * @return Collection|Depot[]
     */
    public function getDepot(): Collection
    {
        return $this->Depot;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->Depot->contains($depot)) {
            $this->Depot[] = $depot;
            $depot->setUser($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->Depot->contains($depot)) {
            $this->Depot->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getUser() === $this) {
                $depot->setUser(null);
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

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setUser($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->contains($compte)) {
            $this->comptes->removeElement($compte);
            // set the owning side to null (unless already changed)
            if ($compte->getUser() === $this) {
                $compte->setUser(null);
            }
        }

        return $this;
    }

    

   

   

    
    
}
