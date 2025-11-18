<?php
class Don {
    private ?int $id_don;
    private ?int $id_association;
    private ?string $prenom;
    private ?string $nom;
    private ?string $email;
    private ?float $montant;
    private ?DateTime $date_don;

    public function __construct(?int $id_don = null, ?int $id_association = null, ?string $prenom = null, ?string $nom = null, ?string $email = null, ?float $montant = null, ?DateTime $date_don = null) {
        $this->id_don = $id_don;
        $this->id_association = $id_association;
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->email = $email;
        $this->montant = $montant;
        $this->date_don = $date_don;
    }

    // Getters
    public function getIdDon(): ?int { return $this->id_don; }
    public function getIdAssociation(): ?int { return $this->id_association; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function getNom(): ?string { return $this->nom; }
    public function getEmail(): ?string { return $this->email; }
    public function getMontant(): ?float { return $this->montant; }
    public function getDateDon(): ?DateTime { return $this->date_don; }

    public function getNomComplet(): string {
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? '')) ?: 'Anonyme';
    }

    // Setters
    public function setIdDon(?int $v): void { $this->id_don = $v; }
    public function setIdAssociation(?int $v): void { $this->id_association = $v; }
    public function setPrenom(?string $v): void { $this->prenom = $v; }
    public function setNom(?string $v): void { $this->nom = $v; }
    public function setEmail(?string $v): void { $this->email = $v; }
    public function setMontant(?float $v): void { $this->montant = $v; }
    public function setDateDon(?DateTime $v): void { $this->date_don = $v; }
}
?>