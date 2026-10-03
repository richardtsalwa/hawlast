<?php
/**
 * HAWLAST - SAAS session guard.
 *
 * Database / SMTP configuration now lives in the ROOT config.php.
 * This file only preserves the SAAS authentication guard that used to sit
 * inside saas/config.php.
 *
 * hawlast_url() lives in the ROOT hawlastke.php, and this guard calls it below,
 * so hawlastke.php MUST be required here. Entry points include this guard
 * before hawlastke.php, so relying on their include order breaks the redirect.
 *
 * IMPORTANT: this is NOT a credentials file. It must never hold secrets.
 */

// Single source of truth for connection settings, session start and timezone.
require_once dirname(__DIR__) . '/config.php';

// Shared non-secret helpers: hawlast_url(), hawlast_base_url(), BASE_URL.
require_once dirname(__DIR__) . '/hawlastke.php';

// Buffer output so the redirect below can never trigger "headers already sent".
if (!ob_get_level()) {
    ob_start();
}

// Allow unauthenticated POST callbacks from the mpesaapi only.
if (!isset($_SESSION['access_level']) && !isset($_POST['mpesaapi'])) {
    ob_end_clean();
    header('Location: ' . hawlast_url('saas/login.php'));
    exit;
}