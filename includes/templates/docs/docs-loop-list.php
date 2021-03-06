<?php bp_docs_inline_toggle_js() ?>

<?php if ( bp_docs_has_docs() ) : ?>
	<table class="doctable">

	<thead>
		<tr valign="bottom">
			<?php if ( bp_docs_enable_attachments() ) : ?>
				<th scope="column" class="attachment-clip-cell">&nbsp;<span class="screen-reader-text"><?php esc_html_e( 'Anhang', 'bp-docs' ); ?></span></th>
			<?php endif ?>

			<th scope="column" class="title-cell<?php bp_docs_is_current_orderby_class( 'title' ) ?>">
				<a href="<?php bp_docs_order_by_link( 'title' ) ?>"><?php _e( 'Titel', 'bp-docs' ); ?></a>
			</th>

			<th scope="column" class="author-cell<?php bp_docs_is_current_orderby_class( 'author' ) ?>">
				<a href="<?php bp_docs_order_by_link( 'author' ) ?>"><?php _e( 'Autor', 'bp-docs' ); ?></a>
			</th>

			<th scope="column" class="created-date-cell<?php bp_docs_is_current_orderby_class( 'created' ) ?>">
				<a href="<?php bp_docs_order_by_link( 'created' ) ?>"><?php _e( 'Erstellt', 'bp-docs' ); ?></a>
			</th>

			<th scope="column" class="edited-date-cell<?php bp_docs_is_current_orderby_class( 'modified' ) ?>">
				<a href="<?php bp_docs_order_by_link( 'modified' ) ?>"><?php _e( 'Bearbeitet', 'bp-docs' ); ?></a>
			</th>

			<?php do_action( 'bp_docs_loop_additional_th' ) ?>
		</tr>
        </thead>

        <tbody>
	<?php while ( bp_docs_has_docs() ) : bp_docs_the_doc() ?>
 		<tr<?php bp_docs_doc_row_classes(); ?>>
			<?php if ( bp_docs_enable_attachments() ) : ?>
				<td class="attachment-clip-cell">
					<?php bp_docs_attachment_icon() ?>
				</td>
			<?php endif ?>

			<td class="title-cell">
				<a href="<?php bp_docs_doc_link() ?>"><?php the_title() ?></a> <?php bp_docs_doc_trash_notice(); ?>

				<?php if ( bp_docs_get_excerpt_length() ) : ?>
					<?php the_excerpt() ?>
				<?php endif ?>

				<div class="row-actions">
					<?php bp_docs_doc_action_links() ?>
				</div>

				<div class="bp-docs-attachment-drawer" id="bp-docs-attachment-drawer-<?php echo get_the_ID() ?>">
					<?php bp_docs_doc_attachment_drawer() ?>
				</div>
			</td>

			<td class="author-cell">
				<a href="<?php echo bp_core_get_user_domain( get_the_author_meta( 'ID' ) ) ?>" title="<?php echo bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ?>"><?php echo bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ?></a>
			</td>

			<td class="date-cell created-date-cell">
				<?php echo get_the_date() ?>
			</td>

			<td class="date-cell edited-date-cell">
				<?php echo get_the_modified_date() ?>
			</td>

			<?php do_action( 'bp_docs_loop_additional_td' ) ?>
		</tr>
	<?php endwhile ?>
        </tbody>

	</table>

	<div id="bp-docs-pagination">
		<div id="bp-docs-pagination-count">
			<?php printf( __( 'Zeige %1$s-%2$s von %3$s-Dokumenten', 'bp-docs' ), bp_docs_get_current_docs_start(), bp_docs_get_current_docs_end(), bp_docs_get_total_docs_num() ) ?>
		</div>

		<div id="bp-docs-paginate-links">
			<?php bp_docs_paginate_links() ?>
		</div>
	</div>

<?php else: ?>

        <?php if ( bp_docs_current_user_can_create_in_context() ) : ?>
                <p class="no-docs"><?php printf( __( 'F??r diese Ansicht sind keine Dokumente vorhanden. Erstelle ein <a href="%s">Dokument</a>?', 'bp-docs' ), bp_docs_get_create_link() ) ?>
	<?php else : ?>
		<p class="no-docs"><?php _e( 'F??r diese Ansicht sind keine Dokumente vorhanden.', 'bp-docs' ) ?></p>
        <?php endif ?>

<?php endif ?>
