<?php
/**
 * HAWLAST theme functions and definitions.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

define( 'HAWLAST_VERSION', '1.0.0' );

/**
 * Absolute path and URI of the theme, both used many times.
 */
define( 'HAWLAST_DIR', get_template_directory() );
define( 'HAWLAST_URI', get_template_directory_uri() );

/**
 * Universal Analytics property used on the hawlast.com homepage.
 */
define( 'HAWLAST_GA_PROPERTY', 'UA-16346251-1' );

/**
 * Main site URL, used for the cross-site nav and footer links.
 */
define( 'HAWLAST_SITE_URL', 'https://www.hawlast.com' );

/**
 * Brand name used whenever the `blogname` option is empty.
 */
define( 'HAWLAST_SITE_NAME', 'Hawlast Ventures' );

/**
 * Fall back to the brand name when the Site Title option is empty or blank.
 *
 * Without this, an empty `blogname` renders a blank logo alt, an empty
 * `og:site_name`, a title tag of just the tagline, and a footer of
 * " , Nairobi.".
 *
 * @param string $name The stored Site Title.
 * @return string
 */
function hawlast_filter_blogname( $name ) {
	$name = trim( (string) $name );

	return '' === $name ? HAWLAST_SITE_NAME : $name;
}
add_filter( 'option_blogname', 'hawlast_filter_blogname' );

/**
 * Cache-busting version for an asset, based on its last modification time.
 *
 * @param string $relative Path relative to the theme root, without a leading slash.
 * @return string Version string.
 */
function hawlast_asset_version( $relative ) {
	$path = HAWLAST_DIR . '/' . ltrim( $relative, '/' );

	if ( file_exists( $path ) ) {
		return (string) filemtime( $path );
	}

	return HAWLAST_VERSION;
}

/**
 * Theme supports, menus and image sizes.
 */
function hawlast_setup() {
	load_theme_textdomain( 'hawlast', HAWLAST_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 60,
			'width'       => 252,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'hawlast' ),
			'footer'  => __( 'Footer menu', 'hawlast' ),
		)
	);

	// Cropped sizes used by the card and hero layouts.
	add_image_size( 'hawlast-card', 640, 360, true );
	add_image_size( 'hawlast-hero', 1280, 720, true );
}
add_action( 'after_setup_theme', 'hawlast_setup' );

/**
 * Content width, matching the 720px reading column.
 */
function hawlast_content_width() {
	$GLOBALS['content_width'] = 720;
}
add_action( 'after_setup_theme', 'hawlast_content_width', 0 );

/**
 * Remove WordPress bloat from the front end.
 */
function hawlast_remove_bloat() {
	// Emoji detection script and inline emoji styles.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// oEmbed discovery links and the embed iframe script.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );
	add_filter( 'embed_oembed_discover', '__return_false' );

	// Well-known and admin links that have no place on a public blog.
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );

	// Global styles and classic theme styles: this is a classic theme.
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_block_template_skip_link' );

	// The duotone support also prints a global-styles inline block.
	remove_action( 'wp_enqueue_scripts', array( 'WP_Duotone', 'output_global_styles' ), 11 );

	// The s.w.org preconnect is not useful here.
	add_filter( 'wp_resource_hints', '__return_empty_array' );
}
add_action( 'init', 'hawlast_remove_bloat' );

/**
 * Front-end assets. No jQuery, no icon fonts, no third-party requests.
 *
 * The stylesheet is loaded with the deferred strategy so the inline critical CSS
 * in header.php can paint the first screen without waiting for it.
 */
function hawlast_enqueue_assets() {
	wp_enqueue_style(
		'hawlast-style',
		get_stylesheet_uri(),
		array(),
		hawlast_asset_version( 'style.css' )
	);
	wp_style_add_data( 'hawlast-style', 'strategy', 'defer' );

	wp_enqueue_script(
		'hawlast-main',
		HAWLAST_URI . '/assets/js/main.js',
		array(),
		hawlast_asset_version( 'assets/js/main.js' ),
		true
	);
	wp_script_add_data( 'hawlast-main', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'hawlast_enqueue_assets' );

/**
 * Drop block library CSS on views that do not use blocks.
 *
 * Posts written with the Classic Editor, or imported legacy posts, need none of
 * the block library styles. The front page is a list of excerpt cards and has no
 * block content either, so it is treated the same way.
 */
function hawlast_conditional_block_styles() {
	$uses_blocks = false;

	if ( is_singular() ) {
		$post = get_post();

		if ( $post && has_blocks( $post ) ) {
			$uses_blocks = true;
		}
	}

	if ( $uses_blocks ) {
		return;
	}

	foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'global-styles', 'classic-theme-styles' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'hawlast_conditional_block_styles', 100 );

/**
 * Preconnect to Google Analytics, the only third-party origin used.
 *
 * @param array  $hints         URLs to print.
 * @param string $relation_type Type of hint.
 * @return array
 */
function hawlast_resource_hints( $hints, $relation_type ) {
	if ( 'preconnect' === $relation_type && ! hawlast_analytics_is_managed() ) {
		$hints[] = array(
			'href'        => 'https://www.googletagmanager.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $hints;
}
add_filter( 'wp_resource_hints', 'hawlast_resource_hints', 10, 2 );

/**
 * Inline critical CSS and the font preload, printed early in <head>.
 *
 * Covers the header, the page title block and the first screen, so nothing
 * render-blocking is needed to show them.
 */
function hawlast_head_critical() {
	$font_url = HAWLAST_URI . '/assets/fonts/inter-var-latin.woff2';

	$critical = <<<'CSS'
@font-face{font-family:Inter;font-style:normal;font-weight:100 900;font-display:swap;src:url(__FONT__) format('woff2')}
*,::before,::after{box-sizing:border-box}
body{margin:0;background:#FFFFFF;color:#10204A;font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;font-size:17px;line-height:1.6;-webkit-font-smoothing:antialiased}
img,svg,video{max-width:100%;height:auto}img{display:block}a{color:inherit}
:focus-visible{outline:3px solid #E8590C;outline-offset:3px}
.wrap{max-width:1120px;margin:0 auto;padding:0 24px}
.read{max-width:720px;margin:0 auto;padding:0 24px}
.site-main{min-height:40vh}
.site-header{position:sticky;top:0;z-index:20;padding:14px 16px 0;background:linear-gradient(to bottom,#FFFFFF 72%,rgba(255,255,255,0))}
.nav-pill{max-width:1120px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:16px;background:#FFFFFF;border:1px solid #E6E1D8;border-radius:999px;padding:10px 12px 10px 22px;box-shadow:0 6px 24px rgba(11,15,20,.08)}
.nav-logo{display:flex;align-items:center;text-decoration:none;flex-shrink:0}.nav-logo img{height:40px;width:auto}
.nav-links{display:flex;gap:30px;list-style:none;margin:0;padding:0}
.nav-links a{text-decoration:none;font-weight:500;font-size:15px;color:#5B6470}.nav-links a:hover{color:#10204A}
.nav-actions{display:flex;gap:8px;align-items:center}
.nav-toggle{display:none;align-items:center;justify-content:center;width:42px;height:42px;padding:0;background:#FFFFFF;border:1.5px solid #E6E1D8;border-radius:999px;color:#10204A;cursor:pointer}.nav-toggle svg{width:20px;height:20px}
.btn{display:inline-block;text-decoration:none;font-weight:600;font-size:15px;padding:10px 20px;border-radius:999px;border:1.5px solid transparent;white-space:nowrap}
.btn-fill{background:#E8590C;color:#0B0F14}.btn-fill:hover{filter:brightness(1.08);color:#0B0F14}
.btn-line{border-color:#10204A;color:#10204A}.btn-line:hover{background:#10204A;color:#FFFFFF}
.btn-lg{padding:15px 28px;font-size:17px}
.page-head{padding:72px 0 40px;border-bottom:1px solid #E6E1D8;margin-bottom:56px}
.page-head h1{font-size:clamp(34px,6vw,60px);line-height:1.08;letter-spacing:-.03em;font-weight:700;margin:0;max-width:18ch}
.page-head .lede{max-width:60ch;font-size:19px;color:#5B6470;margin:20px 0 0}
CSS;

	$critical .= "\n" . <<<'CSS'
.entry-hero{padding:64px 0 0}
.entry-hero h1{font-size:clamp(34px,5.4vw,56px);line-height:1.08;letter-spacing:-.03em;font-weight:700;margin:14px 0 16px}
.entry-meta{color:#5B6470;font-size:15px;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.post-list{list-style:none;margin:0;padding:0}
@media (max-width:860px){.nav-pill{position:relative;padding-left:16px}.nav-links{display:none;position:absolute;top:100%;left:16px;right:16px;flex-direction:column;gap:0;background:#FFFFFF;border:1px solid #E6E1D8;border-radius:20px;box-shadow:0 10px 30px rgba(11,15,20,.1);padding:8px}.nav-links.is-open{display:flex}.nav-links a{display:block;padding:12px 16px;font-size:16px}.nav-toggle{display:inline-flex}.nav-logo img{height:34px}.page-head{padding-top:48px}}
CSS;

	$critical = str_replace( '__FONT__', esc_url( $font_url ), $critical );

	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( $font_url )
	);

	printf(
		"<style id=\"hawlast-critical\">\n%s\n</style>\n",
		$critical // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static theme CSS with an escaped URL substituted in.
	);
}
add_action( 'wp_head', 'hawlast_head_critical', 1 );

/**
 * True when an analytics plugin is already sending the pageview.
 *
 * Keeps visits from being counted twice when Site Kit, AIOSEO, Exact Metrica
 * or a similar plugin is active.
 *
 * @return bool
 */
function hawlast_analytics_is_managed() {
	$classes = array(
		'Google_Site_Kit',        // Site Kit.
		'Exact\Analytics\Plugin', // Exact Metrica.
		'MI_Admin',               // MonsterInsights.
		'Analytify',              // Analytify.
	);

	foreach ( $classes as $class ) {
		if ( class_exists( $class ) ) {
			return true;
		}
	}

	if ( function_exists( 'googlesitekit' ) || defined( 'GOOGLESITEKIT_VERSION' ) || defined( 'GAM_VERSION' ) || defined( 'MONSTERINSIGHTS_VERSION' ) ) {
		return true;
	}

	// AIOSEO only counts as managed when its analytics module is switched on.
	if ( defined( 'AIOSEO_VERSION' ) && class_exists( 'AIOSEO\Plugin\AIOSEO' ) ) {
		$aioseo = \AIOSEO\Plugin\AIOSEO::instance();

		if ( isset( $aioseo->modules->analytics ) && $aioseo->modules->analytics->is_loaded() ) {
			return true;
		}
	}

	/**
	 * Lets a site owner or plugin suppress the theme snippet.
	 *
	 * @param bool $managed Whether analytics is already handled elsewhere.
	 */
	return (bool) apply_filters( 'hawlast_analytics_is_managed', false );
}

/**
 * Optional GA4 measurement ID.
 *
 * The homepage currently ships a Universal Analytics property, which no longer
 * processes data. Supply a G- ID to switch the theme to gtag.js.
 *
 * @return string Measurement ID, or an empty string.
 */
function hawlast_measurement_id() {
	if ( defined( 'HAWLAST_GA4_ID' ) && HAWLAST_GA4_ID ) {
		return (string) HAWLAST_GA4_ID;
	}

	/**
	 * Filters the GA4 measurement ID used by the theme.
	 *
	 * @param string $measurement_id Measurement ID.
	 */
	return (string) apply_filters( 'hawlast_measurement_id', '' );
}

/**
 * Google Analytics, matching the tag used on the hawlast.com homepage.
 */
function hawlast_analytics() {
	if ( hawlast_analytics_is_managed() ) {
		return;
	}

	$measurement_id = hawlast_measurement_id();

	if ( '' === $measurement_id ) {
		?>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
ga('create', '<?php echo esc_js( HAWLAST_GA_PROPERTY ); ?>', 'auto');
ga('send', 'pageview');
</script>
		<?php
		return;
	}

	?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( rawurlencode( $measurement_id ) ); ?>"></script>
<script>
window.dataLayer=window.dataLayer||[];
function gtag(){dataLayer.push(arguments);}
gtag('js',new Date());
gtag('config','<?php echo esc_js( $measurement_id ); ?>');
</script>
	<?php
}
add_action( 'wp_head', 'hawlast_analytics', 20 );

/**
 * Site verification tag from the hawlast.com homepage, on the front page only.
 */
function hawlast_site_verification() {
	if ( ! is_front_page() ) {
		return;
	}

	printf(
		'<meta name="google-site-verification" content="%s">' . "\n",
		esc_attr( 'YZPXJE-6W2iSvgrFkQPcGRTh3md86Qv7PnNa0DTcA2g' )
	);
}
add_action( 'wp_head', 'hawlast_site_verification', 2 );

/**
 * True when an SEO plugin is already outputting canonical, Open Graph and
 * Twitter tags, so the theme does not duplicate them.
 *
 * @return bool
 */
function hawlast_seo_plugin_active() {
	$constants = array(
		'AIOSEO_VERSION',
		'WPSEO_VERSION',
		'RANK_MATH_VERSION',
		'SEOPRESS_VERSION',
		'THE_SEO_FRAMEWORK_VERSION',
		'RANKIE_VERSION',
	);

	foreach ( $constants as $constant ) {
		if ( defined( $constant ) ) {
			return true;
		}
	}

	if ( class_exists( 'AIOSEO\\Plugin\\AIOSEO' ) || class_exists( 'WPSEO_Frontend' ) || function_exists( 'rank_math' ) ) {
		return true;
	}

	/**
	 * Filters whether the theme should skip its own SEO meta tags.
	 *
	 * @param bool $active Whether an SEO plugin is active.
	 */
	return (bool) apply_filters( 'hawlast_seo_plugin_active', false );
}

/**
 * Canonical URL, Open Graph and Twitter Card tags.
 *
 * Skipped when an SEO plugin is active, so tags are never output twice.
 */
function hawlast_meta_tags() {
	if ( hawlast_seo_plugin_active() ) {
		return;
	}

	if ( is_singular() ) {
		$post_id   = get_queried_object_id();
		$title     = get_the_title( $post_id );
		$excerpt   = hawlast_meta_description( $post_id );
		$canonical = get_permalink( $post_id );
		$image     = hawlast_featured_image_url( $post_id );
		$type      = is_singular( 'post' ) ? 'article' : 'website';
	} elseif ( is_home() || is_front_page() ) {
		$title     = get_bloginfo( 'name', 'display' );
		$excerpt   = hawlast_meta_description();
		$canonical = hawlast_home_url();
		$image     = hawlast_default_image();
		$type      = 'website';
	} else {
		$title     = wp_get_document_title();
		$excerpt   = hawlast_meta_description();
		$canonical = hawlast_current_url();
		$image     = hawlast_default_image();
		$type      = 'website';
	}

	if ( '' === $title ) {
		$title = get_bloginfo( 'name', 'display' );
	}

	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );

	printf(
		'<meta property="og:type" content="%s">' . "\n" .
		'<meta property="og:title" content="%s">' . "\n" .
		'<meta property="og:description" content="%s">' . "\n" .
		'<meta property="og:url" content="%s">' . "\n" .
		'<meta property="og:site_name" content="%s">' . "\n" .
		'<meta property="og:locale" content="%s">' . "\n",
		esc_attr( $type ),
		esc_attr( $title ),
		esc_attr( $excerpt ),
		esc_url( $canonical ),
		esc_attr( get_bloginfo( 'name', 'display' ) ),
		esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) )
	);

	if ( $image ) {
		printf(
			'<meta property="og:image" content="%s">' . "\n",
			esc_url( $image )
		);
	}

	if ( is_singular( 'post' ) ) {
		printf(
			'<meta property="article:published_time" content="%s">' . "\n" .
			'<meta property="article:modified_time" content="%s">' . "\n",
			esc_attr( get_the_date( 'c', get_queried_object_id() ) ),
			esc_attr( get_the_modified_date( 'c', get_queried_object_id() ) )
		);
	}

	printf(
		'<meta name="twitter:card" content="summary_large_image">' . "\n" .
		'<meta name="twitter:title" content="%s">' . "\n" .
		'<meta name="twitter:description" content="%s">' . "\n",
		esc_attr( $title ),
		esc_attr( $excerpt )
	);

	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'hawlast_meta_tags', 3 );

/**
 * JSON-LD Article schema on single posts.
 */
function hawlast_json_ld() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post_id = get_queried_object_id();
	$image   = hawlast_featured_image_url( $post_id );

	$data = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'headline'         => get_the_title( $post_id ),
		'description'      => hawlast_meta_description( $post_id ),
		'datePublished'    => get_the_date( 'c', $post_id ),
		'dateModified'     => get_the_modified_date( 'c', $post_id ),
		'url'              => get_permalink( $post_id ),
		'mainEntityOfPage' => get_permalink( $post_id ),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name', 'display' ),
			'url'   => HAWLAST_SITE_URL,
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => HAWLAST_URI . '/assets/img/hawlast-mark-512.png',
			),
		),
	);

	if ( $image ) {
		$data['image'] = array( $image );
	}

	$terms = get_the_terms( $post_id, 'category' );

	if ( $terms && ! is_wp_error( $terms ) ) {
		$data['articleSection'] = wp_list_pluck( $terms, 'name' );
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded.
	);
}
add_action( 'wp_head', 'hawlast_json_ld', 4 );

/**
 * The blog home URL, always absolute.
 *
 * @return string
 */
function hawlast_home_url() {
	$id = (int) get_option( 'page_for_posts' );

	if ( $id ) {
		return get_permalink( $id );
	}

	return home_url( '/' );
}

/**
 * Current request URL, used as a canonical fallback.
 *
 * @return string
 */
function hawlast_current_url() {
	global $wp;

	$path = isset( $wp->request ) ? $wp->request : '';

	return $path ? home_url( '/' . ltrim( $path, '/' ) ) : hawlast_home_url();
}

/**
 * Description used in meta tags.
 *
 * @param int $post_id Optional post ID.
 * @return string
 */
function hawlast_meta_description( $post_id = 0 ) {
	if ( $post_id ) {
		$excerpt = get_the_excerpt( $post_id );

		if ( $excerpt ) {
			return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $excerpt ) ), 40, '...' );
		}
	}

	$description = get_bloginfo( 'description', 'display' );

	return $description ? $description : 'Practical notes on websites, hosting and running a Kenyan business on modern software.';
}

/**
 * Featured image URL at hero size, or an empty string when the post has none.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function hawlast_featured_image_url( $post_id ) {
	if ( ! has_post_thumbnail( $post_id ) ) {
		return '';
	}

	$attachment_id = get_post_thumbnail_id( $post_id );
	$src           = wp_get_attachment_image_src( $attachment_id, 'hawlast-hero' );

	return isset( $src[0] ) ? esc_url_raw( $src[0] ) : '';
}

/**
 * Fallback social image.
 *
 * @return string
 */
function hawlast_default_image() {
	return HAWLAST_URI . '/assets/img/hawlast-mark-512.png';
}

/**
 * Whether the current image is the first one on the page.
 *
 * The first image loads eagerly, every later image lazy-loads.
 *
 * @return bool
 */
function hawlast_is_first_image() {
	static $seen = false;

	$first = ( ! $seen );

	$seen = true;

	return $first;
}

/**
 * Alt text for a post thumbnail, falling back to the post title.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function hawlast_thumbnail_alt( $post_id ) {
	$attachment_id = get_post_thumbnail_id( $post_id );
	$alt           = $attachment_id ? get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) : '';

	if ( $alt ) {
		return (string) $alt;
	}

	return (string) get_the_title( $post_id );
}

/**
 * Featured image for a post card.
 *
 * @param int $post_id Post ID.
 */
function hawlast_card_thumbnail( $post_id ) {
	if ( ! has_post_thumbnail( $post_id ) ) {
		return;
	}

	$loading = hawlast_is_first_image() ? 'eager' : 'lazy';

	echo '<div class="card__media">';
	echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core escapes all attributes.
		get_post_thumbnail_id( $post_id ),
		'hawlast-card',
		false,
		array(
			'class'    => 'card__img',
			'alt'      => hawlast_thumbnail_alt( $post_id ),
			'loading'  => $loading,
			'decoding' => 'async',
			'sizes'    => '(min-width: 861px) 320px, 100vw',
		)
	);
	echo '</div>';
}

/**
 * Featured image for a single post: high priority, never lazy.
 *
 * @param int $post_id Post ID.
 */
function hawlast_hero_image( $post_id ) {
	if ( ! has_post_thumbnail( $post_id ) ) {
		return;
	}

	echo '<figure class="entry-figure">';
	echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core escapes all attributes.
		get_post_thumbnail_id( $post_id ),
		'hawlast-hero',
		false,
		array(
			'alt'           => hawlast_thumbnail_alt( $post_id ),
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'decoding'      => 'async',
			'sizes'         => '(max-width: 768px) 100vw, 720px',
		)
	);
	echo '</figure>';
}

/**
 * Short, escaped excerpt for post cards.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function hawlast_card_excerpt( $post_id ) {
	$excerpt = get_the_excerpt( $post_id );

	if ( ! $excerpt ) {
		$excerpt = get_post_field( 'post_content', $post_id );
	}

	$excerpt = wp_strip_all_tags( strip_shortcodes( $excerpt ) );

	return esc_html( wp_trim_words( $excerpt, 32, '...' ) );
}

/**
 * Prefer WebP for uploaded images when the server supports it.
 *
 * @param string $format Source mime type.
 * @return string
 */
function hawlast_preferred_image_format( $format ) {
	$convertible = array( 'image/jpeg', 'image/png' );

	if ( in_array( $format, $convertible, true ) && function_exists( 'imagewebp' ) ) {
		return 'image/webp';
	}

	return $format;
}
add_filter( 'image_editor_output_format', 'hawlast_preferred_image_format' );

/**
 * Keep the excerpt ending tidy.
 *
 * @param string $more Current "more" string.
 * @return string
 */
function hawlast_excerpt_more( $more ) {
	return is_admin() ? $more : '...';
}
add_filter( 'excerpt_more', 'hawlast_excerpt_more' );

/**
 * Add the pingback link on singular views that accept pings.
 */
function hawlast_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'hawlast_pingback_header', 5 );

/**
 * Fallback menu used when no "primary" menu has been assigned yet.
 *
 * Mirrors the links on the hawlast.com homepage so the header never looks broken.
 */
function hawlast_primary_menu_fallback() {
	echo '<ul id="primary-menu" class="nav-links">';

	$items = array(
		__( 'Blog', 'hawlast' )   => hawlast_home_url(),
		__( 'Services', 'hawlast' ) => HAWLAST_SITE_URL . '/#solutions',
		__( 'AlbaERP', 'hawlast' ) => HAWLAST_SITE_URL . '/#golderp',
		__( 'Work', 'hawlast' )    => HAWLAST_SITE_URL . '/#work',
		__( 'Pricing', 'hawlast' ) => HAWLAST_SITE_URL . '/#pricing',
	);

	foreach ( $items as $label => $url ) {
		printf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	echo '</ul>';
}