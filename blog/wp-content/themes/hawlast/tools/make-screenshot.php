<?php
/**
 * Generates screenshot.png (1200x900) for the HAWLAST theme.
 *
 * Run once from the command line:
 *   "d:\xampp\php\php.exe" tools\make-screenshot.php
 *
 * Draws the brand mark and wordmark with GD so the screenshot matches the
 * logo files shipped with the theme.
 *
 * A TrueType copy of Inter must be supplied through the HAWLAST_FONT
 * environment variable, for example:
 *
 *   set HAWLAST_FONT=C:\path\to\Inter.ttf
 *   "d:\xampp\php\php.exe" tools\make-screenshot.php
 *
 * The font is only needed to regenerate this file, it is not shipped.
 */

if ( ! function_exists( 'imagecreatetruecolor' ) ) {
	fwrite( STDERR, "The GD extension is required.\n" );
	exit( 1 );
}

$theme = dirname( __DIR__ );

$img = imagecreatetruecolor( 1200, 900 );

$white = imagecolorallocate( $img, 0xFF, 0xFF, 0xFF );
$ink = imagecolorallocate( $img, 0x10, 0x20, 0x4A );
$orange = imagecolorallocate( $img, 0xE8, 0x59, 0x0C );
$muted = imagecolorallocate( $img, 0x5B, 0x64, 0x70 );
$soft = imagecolorallocate( $img, 0xFB, 0xF7, 0xF1 );
$line = imagecolorallocate( $img, 0xE6, 0xE1, 0xD8 );

imagefilledrectangle( $img, 0, 0, 1199, 899, $white );

// Warm tint band at the foot, echoing the homepage mission block.
imagefilledrectangle( $img, 0, 660, 1200, 900, $soft );
imageline( $img, 0, 660, 1200, 660, $line );

$font = getenv( 'HAWLAST_FONT' );

if ( ! $font || ! file_exists( $font ) ) {
	fwrite( STDERR, "Set HAWLAST_FONT to a TrueType copy of Inter first.\n" );
	exit( 1 );
}

/**
 * Draws a rounded rectangle.
 *
 * @param resource|GdImage $im     Image.
 * @param int              $x1     Left.
 * @param int              $y1     Top.
 * @param int              $x2     Right.
 * @param int              $y2     Bottom.
 * @param int              $radius Corner radius.
 * @param int              $color  Colour.
 */
function hawlast_rounded_rect( $im, $x1, $y1, $x2, $y2, $radius, $color ) {
	imagefilledrectangle( $im, $x1 + $radius, $y1, $x2 - $radius, $y2, $color );
	imagefilledrectangle( $im, $x1, $y1 + $radius, $x2, $y2 - $radius, $color );

	$d = $radius * 2;
	imagefilledellipse( $im, $x1 + $radius, $y1 + $radius, $d, $d, $color );
	imagefilledellipse( $im, $x2 - $radius, $y1 + $radius, $d, $d, $color );
	imagefilledellipse( $im, $x1 + $radius, $y2 - $radius, $d, $d, $color );
	imagefilledellipse( $im, $x2 - $radius, $y2 - $radius, $d, $d, $color );
}

// Logo mark: orange rounded square with navy strokes, matching hawlast-mark.svg.
$mark_x = 300;
$mark_y = 250;
$mark_w = 180;

hawlast_rounded_rect( $img, $mark_x, $mark_y, $mark_x + $mark_w, $mark_y + $mark_w, 48, $orange );

$bar = 24;
$pad = 45;
$inner = $mark_w - ( $pad * 2 );

imagefilledrectangle( $img, $mark_x + $pad, $mark_y + $pad, $mark_x + $pad + $bar, $mark_y + $pad + $inner, $ink );
imagefilledrectangle( $img, $mark_x + $pad + $inner - $bar, $mark_y + $pad, $mark_x + $pad + $inner, $mark_y + $pad + $inner, $ink );
imagefilledrectangle( $img, $mark_x + $pad, $mark_y + ( $mark_w / 2 ) - ( $bar / 2 ), $mark_x + $pad + $inner, $mark_y + ( $mark_w / 2 ) + ( $bar / 2 ), $ink );
imagefilledellipse( $img, $mark_x + $pad + $inner - ( $bar / 2 ), $mark_y + $pad + ( $bar / 2 ), 26, 26, $white );

// Wordmark. The period is placed using the measured width of "Hawlast".
$wordmark = 'Hawlast';
$wordmark_size = 76;
$box = imagettfbbox( $wordmark_size, 0, $font, $wordmark );

imagettftext( $img, $wordmark_size, 0, 520, 350, $ink, $font, $wordmark );
imagettftext( $img, $wordmark_size, 0, 520 + ( $box[2] - $box[0] ) + 2, 352, $orange, $font, '.' );

// Letterspaced VENTURES.
$word = 'VENTURES';
$letter_spacing = 9;
$size = 22;
$cursor = 524;

for ( $i = 0, $len = strlen( $word ); $i < $len; $i++ ) {
	$box = imagettfbbox( $size, 0, $font, $word[ $i ] );
	imagettftext( $img, $size, 0, $cursor, 392, $muted, $font, $word[ $i ] );
	$cursor += ( $box[2] - $box[0] ) + $letter_spacing;
}

// Headline and description, mirroring the blog front page.
imagettftext( $img, 46, 0, 200, 530, $ink, $font, 'Notes on running a' );
imagettftext( $img, 46, 0, 200, 588, $ink, $font, 'Kenyan business.' );

imagettftext( $img, 22, 0, 200, 740, $muted, $font, 'Websites, email, M-Pesa and ERP, designed well' );
imagettftext( $img, 22, 0, 200, 772, $muted, $font, 'and kept running at 99.95% uptime.' );

// Pills: solid orange, and outlined navy on white.
hawlast_rounded_rect( $img, 200, 812, 420, 872, 30, $orange );

hawlast_rounded_rect( $img, 444, 812, 660, 872, 30, $white );
hawlast_rounded_rect( $img, 445, 813, 659, 871, 29, $ink );
hawlast_rounded_rect( $img, 449, 817, 655, 867, 25, $white );

imagettftext( $img, 22, 0, 236, 850, $ink, $font, 'Book a call' );
imagettftext( $img, 22, 0, 480, 850, $ink, $font, 'Client login' );

$out = $theme . '/screenshot.png';
imagepng( $img, $out, 9 );
imagedestroy( $img );

echo 'Wrote ' . $out . ' (' . filesize( $out ) . " bytes)\n";