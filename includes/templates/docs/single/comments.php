<?php

/**
 * Mostly borrowed from BuddyPress Default
 *
 */

// If this is a history or edit page, bail
if ( ! bp_docs_is_doc_read() ) {
	return;
}

$num_comments = 0;
$num_trackbacks = 0;
foreach ( (array)$comments as $comment ) {
	if ( 'comment' != get_comment_type() )
		$num_trackbacks++;
	else
		$num_comments++;
}

?>

<?php if ( current_user_can( 'bp_docs_read_comments' ) ) : ?>
	<div id="comments" class="comments-area">
		<h3>
			<?php printf( __( 'Diskussion (%d)', 'bp-docs' ), $num_comments ) ?>
		</h3>

		<?php do_action( 'bp_before_blog_comment_list' ) ?>

		<?php if ( have_comments() ) : ?>

			<ol class="commentlist">
				<?php bp_docs_list_comments() ?>
			</ol><!-- .comment-list -->

			<?php do_action( 'bp_after_blog_comment_list' ) ?>

			<?php if ( get_option( 'page_comments' ) ) : ?>
				<div class="comment-navigation paged-navigation">
					<?php paginate_comments_links() ?>
				</div>
			<?php endif; ?>

		<?php else : ?>

			<p class="comments-closed comments-empty">
				<?php _e( 'Es gibt noch keine Kommentare zu diesem Dokument.', 'bp-docs' ) ?>
			</p>

		<?php endif ?>

		<?php if ( current_user_can( 'bp_docs_post_comments' ) ) : ?>
			<?php comment_form( array(), get_the_ID() ) ?>
		<?php else : ?>
			<p class="comments-closed comment-posting-disabled">
				<?php _e( 'Das Posten von Kommentaren in diesem Dokument wurde deaktiviert.', 'bp-docs' ) ?>
			</p>
		<?php endif; ?>

	</div><!-- #comments -->

<?php else : ?>
	<p class="comments-closed comment-display-disabled">
		<?php _e( 'Die Kommentaranzeige in diesem Dokument wurde deaktiviert.', 'bp-docs' ) ?>
	</p>

<?php endif; ?>
