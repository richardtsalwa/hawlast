<?php
/**
 * Comments template.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

/*
 * A password protected post should not leak its discussion. Returning early
 * here also stops comment_form() from rendering an empty form.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area read">
	<?php if ( have_comments() ) : ?>
		<h2 class="comment-reply-title">
			<?php
			$hawlast_comment_count = get_comments_number();

			printf(
				esc_html(
					/* translators: %s: comment count. */
					_n( '%s comment', '%s comments', $hawlast_comment_count, 'hawlast' )
				),
				esc_html( number_format_i18n( $hawlast_comment_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => esc_html__( 'Older comments', 'hawlast' ),
				'next_text' => esc_html__( 'Newer comments', 'hawlast' ),
			)
		);
		?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_submit'  => 'submit btn btn-fill',
			'title_reply'   => esc_html__( 'Leave a comment', 'hawlast' ),
			'title_reply_to'=> esc_html__( 'Reply to %s', 'hawlast' ),
			'cancel_reply_link' => esc_html__( 'Cancel reply', 'hawlast' ),
			'label_submit'  => esc_html__( 'Post comment', 'hawlast' ),
		)
	);
	?>
</div>