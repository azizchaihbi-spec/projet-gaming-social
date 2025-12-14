<?php

class Theme {
    private ?int $id_theme;
    private ?string $nom_theme;
    private ?string $description;
    private ?string $image_url;
    private ?string $icon_url;
    private ?string $couleur;
    private ?DateTime $date_creation;

    public function __construct(
        ?int $id_theme = null,
        ?string $nom_theme = null,
        ?string $description = null,
        ?string $image_url = null,
        ?string $icon_url = null,
        ?string $couleur = null,
        $date_creation = null
    ) {
        $this->id_theme = $id_theme;
        $this->nom_theme = $nom_theme;
        $this->description = $description;
        $this->image_url = $image_url;
        $this->icon_url = $icon_url;
        $this->couleur = $couleur;
        $this->date_creation = $this->toDateTime($date_creation);
    }

    private function toDateTime($value): ?DateTime {
        if ($value === null) return null;
        if ($value instanceof DateTime) return $value;
        try {
            return new DateTime($value);
        } catch (Exception $e) {
            return null;
        }
    }

    // Getters
    public function getIdTheme(): ?int { return $this->id_theme; }
    public function getNomTheme(): ?string { return $this->nom_theme; }
    public function getDescription(): ?string { return $this->description; }
    public function getImageUrl(): ?string { return $this->image_url; }
    public function getIconUrl(): ?string { return $this->icon_url; }
    public function getCouleur(): ?string { return $this->couleur; }
    public function getDateCreation(): ?DateTime { return $this->date_creation; }

    // Setters
    public function setIdTheme(?int $id_theme): void { $this->id_theme = $id_theme; }
    public function setNomTheme(?string $nom_theme): void { $this->nom_theme = $nom_theme; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setImageUrl(?string $image_url): void { $this->image_url = $image_url; }
    public function setIconUrl(?string $icon_url): void { $this->icon_url = $icon_url; }
    public function setCouleur(?string $couleur): void { $this->couleur = $couleur; }
}
