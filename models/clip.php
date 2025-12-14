<?php

class Clip {
    private ?int $id_clip;
    private ?int $id_stream;
    private ?string $titre;
    private ?string $description;
    private ?string $url_video;
    private ?DateTime $date_creation;
    private ?int $nb_vues;
    private ?int $nb_likes;

    public function __construct(
        ?int $id_clip = null,
        ?int $id_stream = null,
        ?string $titre = null,
        ?string $description = null,
        ?string $url_video = null,
        $date_creation = null,
        ?int $nb_vues = 0,
        ?int $nb_likes = 0
    ) {
        $this->id_clip = $id_clip;
        $this->id_stream = $id_stream;
        $this->titre = $titre;
        $this->description = $description;
        $this->url_video = $url_video;
        $this->date_creation = $this->toDateTime($date_creation);
        $this->nb_vues = $nb_vues ?? 0;
        $this->nb_likes = $nb_likes ?? 0;
    }

    private function toDateTime($value): ?DateTime {
        if ($value instanceof DateTime) return $value;
        if (is_string($value) && $value !== '') {
            try { return new DateTime($value); } catch (Exception $e) { return null; }
        }
        return null;
    }

    // Getters
    public function getIdClip(): ?int { return $this->id_clip; }
    public function getIdStream(): ?int { return $this->id_stream; }
    public function getTitre(): ?string { return $this->titre; }
    public function getDescription(): ?string { return $this->description; }
    public function getUrlVideo(): ?string { return $this->url_video; }
    public function getDateCreation(): ?DateTime { return $this->date_creation; }
    public function getNbVues(): ?int { return $this->nb_vues; }
    public function getNbLikes(): ?int { return $this->nb_likes; }

    // Setters
    public function setIdClip(?int $id): void { $this->id_clip = $id; }
    public function setIdStream(?int $id): void { $this->id_stream = $id; }
    public function setTitre(?string $titre): void { $this->titre = $titre ? trim($titre) : null; }
    public function setDescription(?string $desc): void { $this->description = $desc ? trim($desc) : null; }
    public function setUrlVideo(?string $url): void { $this->url_video = $url ? trim($url) : null; }
    public function setDateCreation($date): void { $this->date_creation = $this->toDateTime($date); }
    public function setNbVues(?int $nb): void { $this->nb_vues = max(0, $nb ?? 0); }
    public function setNbLikes(?int $nb): void { $this->nb_likes = max(0, $nb ?? 0); }

    // Utility methods
    public function incrementerVues(int $nb = 1): void {
        $this->nb_vues += max(0, $nb);
    }

    public function incrementerLikes(int $nb = 1): void {
        $this->nb_likes += max(0, $nb);
    }
}
