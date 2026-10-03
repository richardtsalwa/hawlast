<?php
/**
 * Shared main navigation.
 *
 * index.php keeps its own <head> but includes this file, so the menu markup is
 * literally the same on the homepage and on every page built from
 * includes/site_head.php (buyairtime.php, 404.php, terms-of-service.php,
 * privacy-policy.php).
 *
 * On the homepage the section links stay plain fragments so clicking them
 * scrolls instead of reloading. On any other page they point back at the
 * homepage section they belong to.
 */
$esc_nav = static function (string $url): string {
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
};

$onHome = basename(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '')) === 'index.php';
// Other pages get an absolute link back to the homepage section, hence the
// trailing slash so "/hawlast#solutions" cannot be produced.
$home   = $onHome ? '' : hawlast_url() . '/';

$sections = [
    'Solutions' => '#solutions',
    'AlbaERP'   => '#golderp',
    'Work'      => '#work',
    'Pricing'   => '#pricing',
];
?>
<header>
  <nav aria-label="Main">
    <a class="logo" href="<?php echo $esc_nav( $home === '' ? '#top' : $home ); ?>"><img src="<?php echo $esc_nav( hawlast_url( 'images/hawlast-logo.svg' ) . '?v=2' ); ?>" alt="Hawlast Ventures" width="168" height="40"></a>
    <div class="links">
<?php foreach ($sections as $label => $fragment): ?>
      <a href="<?php echo $esc_nav( $home . $fragment ); ?>"><?php echo $label; ?></a>
<?php endforeach; ?>
    </div>
    <div class="actions">
      <a class="btn btn-line hide-s" href="<?php echo $esc_nav( hawlast_url( 'login' ) ); ?>">Client login</a>
      <a class="btn btn-fill" href="<?php echo $esc_nav( hawlast_url( 'book' ) ); ?>">Book a call</a>
    </div>
  </nav>
</header>