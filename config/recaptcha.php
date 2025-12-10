<?php
// Google reCAPTCHA v2 configuration
// reCAPTCHA v2 uses a visible checkbox "I'm not a robot"
// Get your keys from https://www.google.com/recaptcha/admin
if (!defined('RECAPTCHA_SITE_KEY')) {
    define('RECAPTCHA_SITE_KEY', '6LdPYx8sAAAAAIA7z7Nh9FOcT0HAcn9zy4UDkmXY');
}
if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', '6LdPYx8sAAAAANMlFk5lttme5SS5IvUbrU7s-b9O');
}

// Toggle to quickly disable verification in dev environments
if (!defined('RECAPTCHA_ENABLED')) {
    define('RECAPTCHA_ENABLED', true);
}
