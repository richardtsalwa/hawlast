<?php
/**
 * libraries.php - SINGLE include point for every 3rd party library in Hawlast.
 *
 * From ANY file, at ANY folder depth, use one of these:
 *
 *   // root-level file (hawlastke.php, receipt.php, index.php ...)
 *   require_once __DIR__ . '/libraries.php';
 *
 *   // one folder deep (saas/bal.php, a/mpesa.php, admin/domain.php ...)
 *   require_once dirname(__DIR__) . '/libraries.php';
 *
 *   // deeper (saas/airtime/buy.php, karibu/xsend.php ...)
 *   require_once dirname(__DIR__, 2) . '/libraries.php';
 *
 * That single line gives you:
 *   - PHPMailer 6.x        new PHPMailer(true)                     (global alias kept)
 *   - dompdf 3.x           new \Dompdf\Dompdf()
 *   - Africa's Talking SDK new AfricasTalking\SDK\AfricasTalking(...)
 *
 * Libraries themselves live in /vendor and are managed by Composer:
 *   composer require <pkg>      # add a package
 *   composer install            # after a git pull / on the cPanel server
 * Do NOT edit anything inside /vendor - those files are overwritten.
 */

if (!defined('HAWLAST_LIBRARIES')) {

    // 1) Composer autoloader - PHPMailer, dompdf, Africa's Talking SDK, Guzzle.
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        http_response_code(500);
        exit('libraries.php: /vendor is missing. Run "composer install" in the Hawlast root folder.');
    }
    require_once __DIR__ . '/vendor/autoload.php';

    // 2) Backward compatibility - PHPMailer 6 is namespaced
    //    (PHPMailer\PHPMailer\PHPMailer) but existing Hawlast code uses the
    //    old global name "new PHPMailer(true)". Alias it once, safely.
    if (!class_exists('PHPMailer', false) && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        class_alias('PHPMailer\PHPMailer\PHPMailer', 'PHPMailer');
    }
    if (!class_exists('SMTP', false) && class_exists('PHPMailer\PHPMailer\SMTP')) {
        class_alias('PHPMailer\PHPMailer\SMTP', 'SMTP');
    }

    define('HAWLAST_LIBRARIES', true);
}
