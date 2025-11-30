<?php
class Challenge {
    private ?int $id_challenge;
    private ?int $id_association;
    private ?string $name;
    private ?float $objectif;
    private ?string $recompense;
    private ?float $progression;

    public function __construct(
        ?int $id_challenge = null,
        ?int $id_association = null,
        ?string $name = null,
        ?float $objectif = null,
        ?string $recompense = null,
        ?float $progression = 0.00
    ) {
        $this->id_challenge = $id_challenge;
        $this->id_association = $id_association;
        $this->name = $name;
        $this->objectif = $objectif;
        $this->recompense = $recompense;
        $this->progression = $progression;
    }

    // Getters
    public function getIdChallenge(): ?int { return $this->id_challenge; }
    public function getIdAssociation(): ?int { return $this->id_association; }
    public function getName(): ?string { return $this->name; }
    public function getObjectif(): ?float { return $this->objectif; }
    public function getRecompense(): ?string { return $this->recompense; }
    public function getProgression(): ?float { return $this->progression; }

    // Setters
    public function setIdChallenge(?int $v): void { $this->id_challenge = $v; }
    public function setIdAssociation(?int $v): void { $this->id_association = $v; }
    public function setName(?string $v): void { $this->name = $v; }
    public function setObjectif(?float $v): void { $this->objectif = $v; }
    public function setRecompense(?string $v): void { $this->recompense = $v; }
    public function setProgression(?float $v): void { $this->progression = $v; }

    public function getPourcentage(): float {
        if ($this->objectif <= 0) return 0;
        return min(100, round(($this->progression / $this->objectif) * 100, 2));
    }
}
?>