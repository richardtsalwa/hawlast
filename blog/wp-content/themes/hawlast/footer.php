<?php
/**
 * The site footer, matching the hawlast.com homepage.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="site-footer">
	<div class="wrap">
		<span><?php echo esc_html( get_bloginfo( 'name', 'display' ) . ', Nairobi.' ); ?></span>

		<?php
		if ( has_nav_menu( 'footer' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-menu',
					'depth'          => 1,
				)
			);
		} else {
			?>
			<ul class="footer-menu">
				<li class="menu-item"><a href="<?php echo esc_url( HAWLAST_SITE_URL . '/domain-hosting/' ); ?>"><?php esc_html_e( 'Domains and hosting', 'hawlast' ); ?></a></li>
				<li class="menu-item"><a href="<?php echo esc_url( hawlast_home_url() ); ?>"><?php esc_html_e( 'Blog', 'hawlast' ); ?></a></li>
				<li class="menu-item"><a href="<?php echo esc_url( HAWLAST_SITE_URL . '/airtime.php' ); ?>"><?php esc_html_e( 'Buy airtime', 'hawlast' ); ?></a></li>
				<li class="menu-item"><a href="<?php echo esc_url( HAWLAST_SITE_URL . '/login' ); ?>"><?php esc_html_e( 'Client login', 'hawlast' ); ?></a></li>
			</ul>
			<?php
		}
		?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>