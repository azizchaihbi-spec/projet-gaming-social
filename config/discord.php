<?php
// config/discord.php - Configuration Discord Webhooks

class DiscordConfig {
    // Remplace ces URLs par tes vrais webhooks Discord
    // Pour créer un webhook: Paramètres Serveur > Intégrations > Webhooks > Nouveau Webhook
    
    const WEBHOOK_EVENTS = 'https://discord.com/api/webhooks/1447996600587653120/X2a92fBXKxj6cUzgoAqIxYOuk5yzAlxmju8BSabbH9s382qmNuEL0gwEEMExg7cseNqt';
    const WEBHOOK_STREAMS = 'https://discord.com/api/webhooks/1447996600587653120/X2a92fBXKxj6cUzgoAqIxYOuk5yzAlxmju8BSabbH9s382qmNuEL0gwEEMExg7cseNqt';
    const WEBHOOK_DONATIONS = 'https://discord.com/api/webhooks/1447996600587653120/X2a92fBXKxj6cUzgoAqIxYOuk5yzAlxmju8BSabbH9s382qmNuEL0gwEEMExg7cseNqt';
    const WEBHOOK_GENERAL = 'https://discord.com/api/webhooks/1447996600587653120/X2a92fBXKxj6cUzgoAqIxYOuk5yzAlxmju8BSabbH9s382qmNuEL0gwEEMExg7cseNqt';
    
    // Couleurs pour les embeds Discord (en hexadécimal)
    const COLOR_EVENT = 0xEC6090;      // Rose
    const COLOR_STREAM = 0x9146FF;     // Violet (Twitch)
    const COLOR_DONATION = 0x28A745;   // Vert
    const COLOR_SUCCESS = 0x00FF00;    // Vert clair
    const COLOR_WARNING = 0xFFA500;    // Orange
    const COLOR_ERROR = 0xFF0000;      // Rouge
}
