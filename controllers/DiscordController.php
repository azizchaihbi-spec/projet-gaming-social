<?php
// controllers/DiscordController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/discord.php';

class DiscordController {
    
    /**
     * Envoie un embed simple avec titre, description et couleur
     */
    public static function sendSimpleEmbed(string $webhookUrl, string $title, string $description, int $color = 0x22d3ee): bool {
        $embed = [
            'title' => $title,
            'description' => $description,
            'color' => $color,
            'timestamp' => date('c')
        ];
        return self::sendEmbed($webhookUrl, $embed);
    }
    
    /**
     * Envoie un message simple à Discord
     */
    public static function sendMessage(string $webhookUrl, string $message): bool {
        $data = ['content' => $message];
        return self::sendWebhook($webhookUrl, $data);
    }
    
    /**
     * Envoie un embed riche à Discord
     */
    public static function sendEmbed(string $webhookUrl, array $embedData): bool {
        $data = ['embeds' => [$embedData]];
        return self::sendWebhook($webhookUrl, $data);
    }
    
    /**
     * Notification : Nouvel événement créé
     */
    public static function notifyNewEvent(array $event): bool {
        $embed = [
            'title' => '🎮 Nouvel Événement Créé !',
            'description' => "**{$event['titre']}**\n\n{$event['theme']}",
            'color' => DiscordConfig::COLOR_EVENT,
            'fields' => [
                [
                    'name' => '📅 Date de début',
                    'value' => date('d/m/Y à H:i', strtotime($event['date_debut'])),
                    'inline' => true
                ],
                [
                    'name' => '🎯 Objectif',
                    'value' => $event['objectif'] ?? 'Non défini',
                    'inline' => true
                ]
            ],
            'thumbnail' => [
                'url' => 'https://i.imgur.com/your-logo.png' // Remplace par ton logo
            ],
            'footer' => [
                'text' => 'Play to Help - Gaming Solidaire',
                'icon_url' => 'https://i.imgur.com/your-icon.png'
            ],
            'timestamp' => date('c')
        ];
        
        return self::sendEmbed(DiscordConfig::WEBHOOK_EVENTS, $embed);
    }
    
    /**
     * Notification : Stream démarre
     */
    public static function notifyStreamLive(array $stream): bool {
        $embed = [
            'title' => '🔴 STREAM EN DIRECT !',
            'description' => "**{$stream['titre']}** vient de démarrer !",
            'url' => $stream['url'] ?? '',
            'color' => DiscordConfig::COLOR_STREAM,
            'fields' => [
                [
                    'name' => '🎮 Plateforme',
                    'value' => $stream['plateforme'] ?? 'Non spécifié',
                    'inline' => true
                ],
                [
                    'name' => '💰 Objectif Dons',
                    'value' => ($stream['objectif_don'] ?? 0) . ' DT',
                    'inline' => true
                ]
            ],
            'image' => [
                'url' => 'https://i.imgur.com/stream-banner.png' // Remplace par image du stream
            ],
            'footer' => [
                'text' => 'Rejoignez maintenant !',
            ],
            'timestamp' => date('c')
        ];
        
        return self::sendEmbed(DiscordConfig::WEBHOOK_STREAMS, $embed);
    }
    
    /**
     * Notification : Nouveau don reçu
     */
    public static function notifyNewDonation(array $donation): bool {
        $amount = $donation['montant'] ?? 0;
        $donorName = $donation['nom_donateur'] ?? 'Anonyme';
        
        $embed = [
            'title' => '💚 Nouveau Don Reçu !',
            'description' => "**{$donorName}** vient de donner **{$amount} DT** !",
            'color' => DiscordConfig::COLOR_DONATION,
            'fields' => [
                [
                    'name' => '🎯 Association',
                    'value' => $donation['association'] ?? 'Non spécifié',
                    'inline' => true
                ],
                [
                    'name' => '📝 Message',
                    'value' => $donation['message'] ?? 'Aucun message',
                    'inline' => false
                ]
            ],
            'thumbnail' => [
                'url' => 'https://i.imgur.com/heart-icon.png'
            ],
            'footer' => [
                'text' => 'Merci pour votre générosité ! 🙏',
            ],
            'timestamp' => date('c')
        ];
        
        return self::sendEmbed(DiscordConfig::WEBHOOK_DONATIONS, $embed);
    }
    
    /**
     * Notification : Objectif de dons atteint
     */
    public static function notifyGoalReached(string $title, float $goalAmount): bool {
        $embed = [
            'title' => '🎉 OBJECTIF ATTEINT ! 🎉',
            'description' => "L'objectif de **{$goalAmount} DT** a été atteint pour **{$title}** !\n\nMerci à tous les donateurs ! 💪",
            'color' => DiscordConfig::COLOR_SUCCESS,
            'image' => [
                'url' => 'https://media.giphy.com/media/g9582DNuQppxC/giphy.gif' // GIF de célébration
            ],
            'footer' => [
                'text' => 'Play to Help - Ensemble on fait la différence !',
            ],
            'timestamp' => date('c')
        ];
        
        return self::sendEmbed(DiscordConfig::WEBHOOK_GENERAL, $embed);
    }
    
    /**
     * Notification : Stream terminé avec stats
     */
    public static function notifyStreamEnded(array $stream, array $stats): bool {
        $embed = [
            'title' => '✅ Stream Terminé',
            'description' => "Le stream **{$stream['titre']}** s'est terminé !",
            'color' => DiscordConfig::COLOR_SUCCESS,
            'fields' => [
                [
                    'name' => '💰 Total collecté',
                    'value' => ($stats['total_dons'] ?? 0) . ' DT',
                    'inline' => true
                ],
                [
                    'name' => '👥 Donateurs',
                    'value' => ($stats['nb_donateurs'] ?? 0) . ' personnes',
                    'inline' => true
                ],
                [
                    'name' => '⏱️ Durée',
                    'value' => ($stats['duree'] ?? 'N/A'),
                    'inline' => true
                ]
            ],
            'footer' => [
                'text' => 'Merci à tous ! ❤️',
            ],
            'timestamp' => date('c')
        ];
        
        return self::sendEmbed(DiscordConfig::WEBHOOK_STREAMS, $embed);
    }
    
    /**
     * Notification : Rappel événement (24h avant)
     */
    public static function notifyEventReminder(array $event): bool {
        $embed = [
            'title' => '⏰ Rappel : Événement demain !',
            'description' => "N'oubliez pas ! **{$event['titre']}** commence demain à " . date('H:i', strtotime($event['date_debut'])),
            'color' => DiscordConfig::COLOR_WARNING,
            'fields' => [
                [
                    'name' => '📍 Inscriptions',
                    'value' => 'Inscrivez-vous maintenant !',
                    'inline' => false
                ]
            ],
            'footer' => [
                'text' => 'Play to Help',
            ],
            'timestamp' => date('c')
        ];
        
        return self::sendEmbed(DiscordConfig::WEBHOOK_EVENTS, $embed);
    }
    
    /**
     * Fonction principale pour envoyer le webhook
     */
    private static function sendWebhook(string $webhookUrl, array $data): bool {
        if (empty($webhookUrl) || strpos($webhookUrl, 'YOUR_WEBHOOK') !== false) {
            error_log('Discord webhook not configured: ' . $webhookUrl);
            return false;
        }
        
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        $options = [
            'http' => [
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => $json,
                'ignore_errors' => true
            ]
        ];
        
        $context = stream_context_create($options);
        $result = @file_get_contents($webhookUrl, false, $context);
        
        // Log l'erreur si échec
        if ($result === false) {
            error_log('Discord webhook failed for: ' . $webhookUrl);
            error_log('Data sent: ' . $json);
        }
        
        return $result !== false;
    }
    
    /**
     * Test de connexion Discord
     */
    public static function testWebhook(string $webhookUrl): bool {
        return self::sendMessage($webhookUrl, '✅ Test réussi ! Play to Help est connecté à Discord.');
    }
}
