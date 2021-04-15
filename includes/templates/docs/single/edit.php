<?php
$doc_id = 0;
$current_doc = bp_docs_get_current_doc();
if ( $current_doc ) {
	$doc_id = $current_doc->ID;
}

$bp_docs_do_theme_compat = is_buddypress() && bp_docs_do_theme_compat( 'single/edit.php' );
if ( ! $bp_docs_do_theme_compat ) : ?>
<div id="buddypress">
<?php endif; ?>

<div class="<?php bp_docs_container_class(); ?>">
	<?php include( bp_docs_locate_template( 'single/sidebar.php' ) ) ?>

	<?php include( apply_filters( 'bp_docs_header_template', bp_docs_locate_template( 'docs-header.php' ) ) ) ?>

	<?php
	// No media support at the moment. Want to integrate with something like BP Group Documents
	// include_once ABSPATH . '/wp-admin/includes/media.php' ;

	if ( !function_exists( 'wp_editor' ) ) {
		require_once ABSPATH . '/wp-admin/includes/post.php' ;
		wp_tiny_mce();
	}

	?>

	<?php do_action( 'template_notices' ) ?>

	<div class="doc-content">

	<form action="" method="post" class="standard-form" id="doc-form">
	    <div class="doc-header">
		<?php if ( bp_docs_is_existing_doc() ) : ?>
			<input type="hidden" id="existing-doc-id" value="<?php echo $doc_id; ?>" />
		<?php endif ?>
	    </div>
	    <div class="doc-content-wrapper">
		<div id="doc-content-title">
			<label for="doc-title"><?php _e( 'Titel', 'bp-docs' ) ?></label>
			<input type="text" id="doc-title" name="doc[title]" class="long" value="<?php bp_docs_edit_doc_title() ?>" />
		</div>

		<?php if ( bp_docs_is_existing_doc() ) : ?>
			<div id="doc-content-permalink">
				<label for="doc-permalink"><?php _e( 'Permalink', 'bp-docs' ) ?></label>
				<code><?php echo trailingslashit( bp_get_root_domain() ) . bp_docs_get_docs_slug() . '/' ?></code><input type="text" id="doc-permalink" name="doc[permalink]" class="long" value="<?php bp_docs_edit_doc_slug() ?>" />
			</div>
		<?php endif ?>

		<?php do_action( 'bp_docs_before_doc_edit_content', $doc_id ) ?>

		<div id="doc-content-textarea">
			<label id="content-label" for="doc_content"><?php _e( 'Inhalt', 'bp-docs' ) ?></label>
			<div id="editor-toolbar">
				<?php
					if ( function_exists( 'wp_editor' ) ) {
						$wp_editor_args = apply_filters( 'bp_docs_wp_editor_args', array(
							'media_buttons' => false,
							'dfw'		=> false,
						) );
						wp_editor( bp_docs_get_edit_doc_content(), 'doc_content', $wp_editor_args );
					} else {
						the_editor( bp_docs_get_edit_doc_content(), 'doc_content', 'doc[title]', false );
					}
				?>
			</div>
		</div>

		<?php do_action( 'bp_docs_after_doc_edit_content', $doc_id ) ?>

		<?php if ( bp_docs_enable_attachments() ) : ?>
			<div id="doc-attachments">
				<label for="insert-media-button"><?php _e( 'Anhänge', 'bp-docs' ) ?></label>
				<?php include ( bp_docs_locate_template( 'single/attachments.php' ) ) ?>
			</div>
		<?php endif ?>

		<div id="doc-meta">
			<?php do_action( 'bp_docs_opening_meta_box', $doc_id ) ?>

			<?php if ( bp_is_active( 'groups' ) && current_user_can( 'bp_docs_manage', $doc_id ) && apply_filters( 'bp_docs_allow_associated_group', true ) ) : ?>
				<?php do_action( 'bp_docs_before_assoc_groups_meta_box', $doc_id ); ?>

				<div id="doc-associated-group" class="doc-meta-box">
					<div class="toggleable <?php bp_docs_toggleable_open_or_closed_class( 'associated-group-meta-box' ) ?>">
						<p class="toggle-switch" id="associated-group-toggle">
							<span class="hide-if-js toggle-link-no-js"><?php _e( 'Assoziierte Gruppe', 'bp-docs' ) ?></span>
							<a class="hide-if-no-js toggle-link" id="associated-toggle-link" href="#"><span class="show-pane plus-or-minus"></span><?php _e( 'Assoziierte Gruppe', 'bp-docs' ) ?></a>
						</p>

						<div class="toggle-content">
							<table class="toggle-table" id="toggle-table-associated-group">
								<?php bp_docs_doc_associated_group_markup() ?>
							</table>
						</div>
					</div>
				</div>

				<?php do_action( 'bp_docs_after_assoc_groups_meta_box', $doc_id ); ?>
			<?php endif ?>

			<?php if ( current_user_can( 'bp_docs_manage', $doc_id ) && apply_filters( 'bp_docs_allow_access_settings', true ) ) : ?>
				<?php do_action( 'bp_docs_before_access_settings_meta_box', $doc_id ) ?>

				<div id="doc-settings" class="doc-meta-box">
					<div class="toggleable <?php bp_docs_toggleable_open_or_closed_class( 'access-meta-box' ) ?>">
						<p class="toggle-switch" id="settings-toggle">
							<span class="hide-if-js toggle-link-no-js"><?php _e( 'Zugriff', 'bp-docs' ) ?></span>
							<a class="hide-if-no-js toggle-link" id="settings-toggle-link" href="#"><span class="show-pane plus-or-minus"></span><?php _e( 'Zugriff', 'bp-docs' ) ?></a>
						</p>

						<div class="toggle-content">
							<table class="toggle-table" id="toggle-table-settings">
								<?php bp_docs_doc_settings_markup() ?>
							</table>
						</div>
					</div>
				</div>

				<?php do_action( 'bp_docs_after_access_settings_meta_box', $doc_id ) ?>
			<?php endif ?>

			<?php do_action( 'bp_docs_before_tags_meta_box', $doc_id ) ?>

			<div id="doc-tax" class="doc-meta-box">
				<div class="toggleable <?php bp_docs_toggleable_open_or_closed_class( 'tags-meta-box' ) ?>">
					<p id="tags-toggle-edit" class="toggle-switch">
						<span class="hide-if-js toggle-link-no-js"><?php _e( 'Tags', 'bp-docs' ) ?></span>
						<a class="hide-if-no-js toggle-link" id="tags-toggle-link" href="#"><span class="show-pane plus-or-minus"></span><?php _e( 'Tags', 'bp-docs' ) ?></a>
					</p>

					<div class="toggle-content">
						<table class="toggle-table" id="toggle-table-tags">
							<tr>
								<td class="desc-column">
									<label for="bp_docs_tag"><?php _e( 'Tags sind Wörter oder Ausdrücke, mit denen Du Deine Dokumente beschreiben und organisieren kannst.', 'bp-docs' ) ?></label>
									<span class="description"><?php _e( 'Sseparate Tags mit Kommas (zum Beispiel: <em>Orchester, Snare Drum, Piccolo, Brahms</em>)', 'bp-docs' ) ?></span>
								</td>

								<td>
									<?php bp_docs_post_tags_meta_box() ?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>

			<?php do_action( 'bp_docs_after_tags_meta_box', $doc_id ) ?>

			<?php if ( apply_filters( 'bp_docs_allow_parent_doc_setting', true, $doc_id ) ) : ?>
				<?php do_action( 'bp_docs_before_parent_meta_box', $doc_id ) ?>

				<div id="doc-parent" class="doc-meta-box">
					<div class="toggleable <?php bp_docs_toggleable_open_or_closed_class( 'parent-meta-box' ) ?>">
						<p class="toggle-switch" id="parent-toggle">
							<span class="hide-if-js toggle-link-no-js"><?php _e( 'Elterndokument', 'bp-docs' ) ?></span>
							<a class="hide-if-no-js toggle-link" id="parent-toggle-link" href="#"><span class="show-pane plus-or-minus"></span><?php _e( 'Elterndokument', 'bp-docs' ) ?></a>
						</p>

						<div class="toggle-content">
							<table class="toggle-table" id="toggle-table-parent">
								<tr>
									<td class="desc-column">
										<label for="parent_id"><?php _e( 'Wähle ein übergeordnetes Dokument für dieses Dokument aus.', 'bp-docs' ) ?></label>

										<span class="description"><?php _e( '(Optional) Das Zuweisen eines übergeordneten Dokuments bedeutet, dass am Ende dieses Dokuments ein Link zum übergeordneten Dokument und am unteren Rand des übergeordneten Dokuments ein Link zu diesem Dokument angezeigt wird.', 'bp-docs' ) ?></span>
									</td>

									<td class="content-column">
										<?php bp_docs_edit_parent_dropdown() ?>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<?php do_action( 'bp_docs_after_parent_meta_box', $doc_id ) ?>
			<?php endif; ?>

			<?php do_action( 'bp_docs_closing_meta_box', $doc_id ) ?>
		</div>

		<div style="clear: both"> </div>

		<div id="doc-submit-options">

			<?php wp_nonce_field( 'bp_docs_save' ) ?>

			<input type="hidden" id="doc_id" name="doc_id" value="<?php echo $doc_id ?>" />
			<input type="submit" name="doc-edit-submit" id="doc-edit-submit" value="<?php _e( 'Speichern', 'bp-docs' ) ?>"> <input type="submit" name="doc-edit-submit-continue" id="doc-edit-submit-continue" value="<?php echo esc_attr( _e( 'Speichern und weiter bearbeiten', 'bp-docs' ) ); ?>" /> <a href="<?php bp_docs_cancel_edit_link() ?>" class="action safe confirm"><?php _e( 'Abbrechen', 'bp-docs' ); ?></a>

			<?php if ( bp_docs_is_existing_doc() ) : ?>
				<?php if ( current_user_can( 'bp_docs_manage', $doc_id ) ) : ?>
					<?php bp_docs_delete_doc_button() ?>
				<?php endif ?>
			<?php endif ?>
		</div>


		<div style="clear: both"> </div>
	    </div>
	</form>

	</div><!-- .doc-content -->

	<?php if ( !function_exists( 'wp_editor' ) ) : ?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		/* On some setups, it helps TinyMCE to load if we fire the switchEditors event on load */
		if ( typeof(switchEditors) == 'object' ) {
			if ( !$("#edButtonPreview").hasClass('active') ) {
				switchEditors.go('doc_content', 'tinymce');
			}
		}
	},(jQuery));
	</script>
	<?php endif ?>

	<?php /* Important - do not remove. Needed for autosave stuff */ ?>
	<div style="display:none;">
	<div id="still_working_content" name="still_working_content">
		<br />
		<h3><?php _e( 'Bist du noch da?', 'bp-docs' ) ?></h3>

		<p><?php _e( 'IUm ein Überschreiben von Inhalten zu verhindern, kann jeweils nur eine Person ein bestimmtes Dokument bearbeiten. Aus diesem Grund musst Du regelmäßig sicherstellen, dass das Dokument, das Du noch aktiv bearbeitest, aktiv ist. Wenn Du länger als 30 Minuten inaktiv bist, werden Deine Änderungen automatisch gespeichert und Du wirst aus dem Bearbeitungsmodus geworfen, damit andere auf das Dokument zugreifen können.', 'bp-docs' ) ?></p>

		<a href="#" onclick="jQuery.colorbox.close(); return false" class="button"><?php _e( 'Ich bearbeite noch!', 'bp-docs' ) ?></a>
	</div>
	</div>

</div><!-- .bp-docs -->

<?php if ( ! $bp_docs_do_theme_compat ) : ?>
</div><!-- /#buddypress -->
<?php endif; ?>
