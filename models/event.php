<?php

class Event {
    private ?int $id_evenement;
    private ?string $titre;
    private ?string $theme;
    private ?string $banner_url;
    private ?string $description;
    private ?DateTime $date_debut;
    private ?DateTime $date_fin;
    private ?float $objectif;

    public function __construct(
        ?int $id_evenement = null,
        ?string $titre = null,
        ?string $theme = null,
        ?string $banner_url = null,
        ?string $description = null,
        $date_debut = null,
        $date_fin = null,
        ?float $objectif = null
    ) {
        $this->id_evenement = $id_evenement;
        $this->titre = $titre;
        $this->theme = $theme;
        $this->banner_url = $banner_url;
        $this->description = $description;
        $this->date_debut = $this->toDateTime($date_debut);
        $this->date_fin = $this->toDateTime($date_fin);
        $this->objectif = $objectif;
    }

    private function toDateTime($value): ?DateTime {
        if ($value instanceof DateTime) return $value;
        if (is_string($value) && $value !== '') {
            try { return new DateTime($value); } catch (Exception $e) { return null; }
        }
        return null;
    }

    public function getIdEvenement(): ?int { return $this->id_evenement; }
    public function getTitre(): ?string { return $this->titre; }
    public function getTheme(): ?string { return $this->theme; }
    public function getBannerUrl(): ?string { return $this->banner_url; }
    public function getDescription(): ?string { return $this->description; }
    public function getDateDebut(): ?DateTime { return $this->date_debut; }
    public function getDateFin(): ?DateTime { return $this->date_fin; }
    public function getObjectif(): ?float { return $this->objectif; }

    public function setIdEvenement(?int $id): void { $this->id_evenement = $id; }
    public function setTitre(?string $titre): void { $this->titre = $titre ? trim($titre) : null; }
    public function setTheme(?string $theme): void { $this->theme = $theme ? trim($theme) : null; }
    public function setBannerUrl(?string $banner_url): void { $this->banner_url = $banner_url ? trim($banner_url) : null; }
    public function setDescription(?string $description): void { $this->description = $description ? trim($description) : null; }
    public function setDateDebut($date): void { $this->date_debut = $this->toDateTime($date); }
    public function setDateFin($date): void { $this->date_fin = $this->toDateTime($date); }
    public function setObjectif(?float $objectif): void { $this->objectif = $objectif; }
}