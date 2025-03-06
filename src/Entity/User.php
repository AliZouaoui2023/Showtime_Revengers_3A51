<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    
    #[ORM\Column(name: 'face_token', type: 'string', length: 255, nullable: true)]
    private ?string $faceToken = null;

    // Other fields...

    public function getFaceToken(): ?string
    {
        return $this->faceToken;
    }

    public function setFaceToken(?string $faceToken): self
    {
        $this->faceToken = $faceToken;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 255)]
    private string $nom;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateDeNaissance;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $role;

    #[ORM\Column(type: 'string', length: 255)]
    private string $motDePasse;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $photo = null;

    /**
     * @var Collection<int, Cour>
     */
    #[ORM\ManyToMany(targetEntity: Cour::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'participation')]
    private Collection $cours;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
    }

    // GETTERS & SETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDeNaissance(): \DateTimeInterface
    {
        return $this->dateDeNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $dateDeNaissance): self
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
   

    /**
     * @return Collection<int, Cour>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cour $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
        }

        return $this;
    }

    public function removeCour(Cour $cour): self
    {
        $this->cours->removeElement($cour);

        return $this;
    }
}