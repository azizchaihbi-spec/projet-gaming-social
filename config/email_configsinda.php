<?php
/**
 * Configuration Email
 */

return [
    // Configuration SMTP
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_auth' => true,
    
    // Identifiants SMTP
    'smtp_username' => 'playtohelp1@gmail.com',
    'smtp_password' => 'ebtisuidbmpddxjb',
    
    // Expéditeur par défaut
    'from_email' => 'playtohelp1@gmail.com',
    'from_name' => 'Play to Help',
    
    // Options
    'charset' => 'UTF-8',
    'debug' => 0,  // 0 = pas de debug, 2 = debug complet
];