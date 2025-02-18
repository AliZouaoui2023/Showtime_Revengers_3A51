<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    const TYPE_FOOTER_WEB = 'footerWeb';
    const TYPE_INTEGREFILM = 'integrefilm';
    const TYPE_BACKDROP = 'backdrop';

    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_APPROUVEE = 'approuvee';
    const STATUT_REJETEE = 'rejete';

    // Tarifs par jour pour chaque type de publicité
    const TARIF_FOOTER_WEB = 8;      // 8 par jour
    const TARIF_INTEGREFILM = 20;    // 20 par jour
    const TARIF_BACKDROP = 10;       // 10 par jour

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?User $admin = null;

    #[ORM\Column]
    private ?int $nbrJours = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lienSupp = null;

    #[ORM\Column(length: 255, options: ["default" => "en_attente"])]
    private ?string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $dateSoumission = null;

 
    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): static { $this->user = $user; return $this; }

    public function getAdmin(): ?User { return $this->admin; }
    public function setAdmin(?User $admin): static { $this->admin = $admin; return $this; }
    
    public function getNbrJours(): ?int { return $this->nbrJours; }
    public function setNbrJours(int $nbrJours): static { $this->nbrJours = $nbrJours; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getLienSupp(): ?string { return $this->lienSupp; }
    public function setLienSupp(?string $lienSupp): static { $this->lienSupp = $lienSupp; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getDateSoumission(): ?\DateTimeInterface { return $this->dateSoumission; }
    public function setDateSoumission(\DateTimeInterface $dateSoumission): static { $this->dateSoumission = $dateSoumission; return $this; }

    // Méthode pour calculer le montant
    public function calculerMontant(): float
    {
        $tarifParJour = 0;

        switch ($this->type) {
            case self::TYPE_FOOTER_WEB:
                $tarifParJour = self::TARIF_FOOTER_WEB;
                break;
            case self::TYPE_INTEGREFILM:
                $tarifParJour = self::TARIF_INTEGREFILM;
                break;
            case self::TYPE_BACKDROP:
                $tarifParJour = self::TARIF_BACKDROP;
                break;
            default:
                // Vous pouvez gérer un cas par défaut si nécessaire
                $tarifParJour = 0;
                break;
        }

        return $tarifParJour * $this->nbrJours; // Calcul du montant total
    }
}
