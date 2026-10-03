<?php
/**
 * The site header, including the floating pill navigation.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

	<link rel="icon" href="<?php echo esc_url( HAWLAST_URI . '/assets/img/hawlast-mark.svg' ); ?>" type="image/svg+xml">
	<link rel="icon" href="<?php echo esc_url( HAWLAST_URI . '/assets/img/hawlast-mark-32.png' ); ?>" sizes="32x32" type="image/png">
	<link rel="apple-touch-icon" href="<?php echo esc_url( HAWLAST_URI . '/assets/img/hawlast-mark-192.png' ); ?>">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'hawlast' ); ?></a>

<header class="site-header">
	<nav class="nav-pill" aria-label="<?php esc_attr_e( 'Main', 'hawlast' ); ?>">
		<?php
		if ( has_custom_logo() ) {
			// The custom logo already outputs its own linked image.
			the_custom_logo();
		} else {
			printf(
				'<a class="nav-logo" href="%s" rel="home"><img src="%s" width="168" height="40" alt="%s" decoding="async" fetchpriority="high"></a>',
				esc_url( HAWLAST_SITE_URL ),
				esc_url( HAWLAST_URI . '/assets/img/hawlast-logo.svg' ),
				esc_attr( get_bloginfo( 'name', 'display' ) )
			);
		}
		?>

		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav-links',
				'depth'          => 2,
				'fallback_cb'    => 'hawlast_primary_menu_fallback',
			)
		);
		?>

		<div class="nav-actions">
			<a class="btn btn-line hide-s" href="<?php echo esc_url( HAWLAST_SITE_URL . '/login' ); ?>"><?php esc_html_e( 'Client login', 'hawlast' ); ?></a>
			<a class="btn btn-fill" href="<?php echo esc_url( HAWLAST_SITE_URL . '/book' ); ?>"><?php esc_html_e( 'Book a call', 'hawlast' ); ?></a>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" data-hawlast-toggle>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'hawlast' ); ?></span>
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true" focusable="false">
					<path d="M3 5.5h14M3 10h14M3 14.5h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
				</svg>
			</button>
		</div>
	</nav>
</header>

<main id="content" class="site-main">