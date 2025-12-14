<?php

class Stream {
    private ?int $id_stream;
    private ?string $titre;
    private ?string $plateforme;
    private ?string $url;
    private ?DateTime $date_debut;
    private ?DateTime $date_fin;
    private ?string $statut;
    private ?int $don_total;
    private ?int $nb_commentaires;
    private ?int $nb_likes;
    private ?int $nb_dislikes;
    private ?int $nb_vues;
    private ?int $nb_notification;

    public function __construct(
        ?int $id_stream = null,
        ?string $titre = null,
        ?string $plateforme = null,
        ?string $url = null,
        $date_debut = null,
        $date_fin = null,
        ?string $statut = 'scheduled',
        ?int $don_total = 0,
        ?int $nb_commentaires = 0,
        ?int $nb_likes = 0,
        ?int $nb_dislikes = 0,
        ?int $nb_vues = 0,
        ?int $nb_notification = 0
    ) {
        $this->id_stream = $id_stream;
        $this->titre = $titre;
        $this->plateforme = $plateforme;
        $this->url = $url;
        $this->date_debut = $this->toDateTime($date_debut);
        $this->date_fin = $this->toDateTime($date_fin);
        $this->statut = $statut;
        $this->don_total = $don_total;
        $this->nb_commentaires = $nb_commentaires ?? 0;
        $this->nb_likes = $nb_likes ?? 0;
        $this->nb_dislikes = $nb_dislikes ?? 0;
        $this->nb_vues = $nb_vues ?? 0;
        $this->nb_notification = $nb_notification ?? 0;
    }

    private function toDateTime($value): ?DateTime {
        if ($value instanceof DateTime) return $value;
        if (is_string($value) && $value !== '') {
            try { return new DateTime($value); } catch (Exception $e) { return null; }
        }
        return null;
    }

    public function getIdStream(): ?int { return $this->id_stream; }
    public function getTitre(): ?string { return $this->titre; }
    public function getPlateforme(): ?string { return $this->plateforme; }
    public function getUrl(): ?string { return $this->url; }
    public function getDateDebut(): ?DateTime { return $this->date_debut; }
    public function getDateFin(): ?DateTime { return $this->date_fin; }
    public function getStatut(): ?string { return $this->statut; }
    public function getDonTotal(): ?int { return $this->don_total; }

    public function setIdStream(?int $id): void { $this->id_stream = $id; }
    public function setTitre(?string $titre): void { $this->titre = $titre ? trim($titre) : null; }
    public function setPlateforme(?string $p): void { $this->plateforme = $p ? trim($p) : null; }
    public function setUrl(?string $url): void { $this->url = $url ? trim($url) : null; }
    public function setDateDebut($d): void { $this->date_debut = $this->toDateTime($d); }
    public function setDateFin($d): void { $this->date_fin = $this->toDateTime($d); }
    public function setStatut(?string $s): void { $this->statut = $s ? trim($s) : null; }
    public function setDonTotal(?int $t): void { $this->don_total = $t ?? 0; }

    public function getNbCommentaires(): ?int { return $this->nb_commentaires; }
    public function getNbLikes(): ?int { return $this->nb_likes; }
    public function getNbDislikes(): ?int { return $this->nb_dislikes; }
    public function getNbVues(): ?int { return $this->nb_vues; }
    public function getNbNotification(): ?int { return $this->nb_notification; }

    public function setNbCommentaires(?int $nb): void { $this->nb_commentaires = max(0, $nb ?? 0); }
    public function setNbLikes(?int $nb): void { $this->nb_likes = max(0, $nb ?? 0); }
    public function setNbDislikes(?int $nb): void { $this->nb_dislikes = max(0, $nb ?? 0); }
    public function setNbVues(?int $nb): void { $this->nb_vues = max(0, $nb ?? 0); }
    public function setNbNotification(?int $nb): void { $this->nb_notification = max(0, $nb ?? 0); }

    public function ajouterCommentaire(): void {
        $this->nb_commentaires++;
    }

    public function ajouterLike(): void {
        $this->nb_likes++;
    }

    public function ajouterDislike(): void {
        $this->nb_dislikes++;
    }

    public function incrementerVues(int $nb = 1): void {
        $this->nb_vues += max(0, $nb);
    }

    public function ajouterNotification(): void {
        $this->nb_notification++;
    }

    public function getEngagementTotal(): int {
        return $this->nb_likes + $this->nb_commentaires - $this->nb_dislikes;
    }

    public function getTauxEngagement(): float {
        if ($this->nb_vues <= 0) return 0.0;
        $engagement = $this->nb_likes + $this->nb_commentaires;
        return round(($engagement / $this->nb_vues) * 100, 2);
    }
}
