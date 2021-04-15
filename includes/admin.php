<?php

/**
 * Dashboard functions for BP Dokumente
 *
 * @package BuddyPressDocs
 * @since 1.1.8
 */

class BP_Docs_Admin {
	/**
	 * Constructor
	 *
	 * @since 1.1.8
	 */
	function __construct() {
		// Replace the Dashboard widget
		if ( !defined( BP_DOCS_REPLACE_RECENT_COMMENTS_DASHBOARD_WIDGET ) || !BP_DOCS_REPLACE_RECENT_COMMENTS_DASHBOARD_WIDGET ) {
			add_action( 'wp_dashboard_setup', array( $this, 'replace_recent_comments_dashboard_widget' ) );
		}

		// Set up menus
		add_action( 'admin_menu', array( $this, 'setup_menus' ) );
		add_action( 'admin_menu', array( $this, 'setup_settings' ) );
	}

	public function setup_menus() {
		// Settings
		add_submenu_page(
			'edit.php?post_type=' . bp_docs_get_post_type_name(),
			__( 'BP Dokumente-Einstellungen', 'bp-docs' ),
			__( 'Einstellungen', 'bp-docs' ),
			'bp_moderate',
			'bp-docs-settings',
			array( $this, 'settings_cb' )
		);
	}

	public function settings_cb() {
		?>
<div class="wrap">
	<form method="post" action="<?php echo admin_url( 'options.php' ) ?>">
		<h2><?php _e( 'BP Dokumente-Einstellungen', 'bp-docs' ) ?></h2>
		<?php settings_fields( 'bp-docs-settings' ) ?>
		<?php do_settings_sections( 'bp-docs-settings' ) ?>
		<?php submit_button() ?>
	</form>
</div>
		<?php
	}

	public function setup_settings() {
		// General
		add_settings_section(
			'bp-docs-general',
			__( 'Allgemein', 'bp-docs' ),
			array( $this, 'general_section' ),
			'bp-docs-settings'
		);

		// General - Docs slug
		add_settings_field(
			'bp-docs-slug',
			__( 'Slug', 'bp-docs' ),
			array( $this, 'slug_setting_markup' ),
			'bp-docs-settings',
			'bp-docs-general'
		);
		register_setting( 'bp-docs-settings', 'bp-docs-slug', array(
			'sanitize_callback' => array( $this, 'sanitize_slug' ),
		) );

		// General - Excerpt length
		add_settings_field(
			'bp-docs-excerpt-length',
			__( 'Verzeichnisauszugslänge', 'bp-docs' ),
			array( $this, 'excerpt_length_setting_markup' ),
			'bp-docs-settings',
			'bp-docs-general'
		);
		register_setting( 'bp-docs-settings', 'bp-docs-excerpt-length', 'absint' );

		// General - Docs Directory title
		add_settings_field(
			'bp-docs-directory-title',
			__( 'Verzeichnistitel', 'bp-docs' ),
			array( $this, 'directory_title_setting_markup' ),
			'bp-docs-settings',
			'bp-docs-general'
		);
		register_setting( 'bp-docs-settings', 'bp-docs-directory-title', 'sanitize_text_field' );

		// Users
		add_settings_section(
			'bp-docs-users',
			__( 'Benutzer', 'bp-docs' ),
			array( $this, 'users_section' ),
			'bp-docs-settings'
		);

		// Users - Tab name
		add_settings_field(
			'bp-docs-user-tab-name',
			__( 'Name der Benutzerregisterkarte', 'bp-docs' ),
			array( $this, 'user_tab_name_setting_markup' ),
			'bp-docs-settings',
			'bp-docs-users'
		);
		register_setting( 'bp-docs-settings', 'bp-docs-user-tab-name' );

		// Groups
		if ( bp_is_active( 'groups' ) ) {
			add_settings_section(
				'bp-docs-groups',
				__( 'Gruppen', 'bp-docs' ),
				array( $this, 'groups_section' ),
				'bp-docs-settings'
			);

			// Groups - Tab name
			add_settings_field(
				'bp-docs-tab-name',
				__( 'Name der Registerkarte Gruppe', 'bp-docs' ),
				array( $this, 'group_tab_name_setting_markup' ),
				'bp-docs-settings',
				'bp-docs-groups'
			);
			register_setting( 'bp-docs-settings', 'bp-docs-tab-name' );
		}

		// Attachments
		add_settings_section(
			'bp-docs-attachments',
			__( 'Anhänge', 'bp-docs' ),
			array( $this, 'attachments_section' ),
			'bp-docs-settings'
		);

		// Users - Tab name
		add_settings_field(
			'bp-docs-enable-attachments',
			__( 'Anhänge aktivieren', 'bp-docs' ),
			array( $this, 'enable_attachments_setting_markup' ),
			'bp-docs-settings',
			'bp-docs-attachments'
		);
		register_setting( 'bp-docs-settings', 'bp-docs-enable-attachments' );
	}

	public function general_section() { settings_errors(); }
	public function users_section() {}
	public function groups_section() {}
	public function attachments_section() {}

	public function slug_setting_markup() {
		global $bp;

		$slug = bp_docs_get_docs_slug();
		$is_in_wp_config = 1 === $bp->bp_docs->slug_defined_in_wp_config['slug'];

		?>
		<label for="bp-docs-slug" class="screen-reader-text"><?php _e( "Ändere den Slug, der zum Erstellen von Dokumenten-URLs verwendet wird.", 'bp-docs' ) ?></label>
		<input name="bp-docs-slug" id="bp-docs-slug" type="text" value="<?php echo esc_html( $slug ) ?>" <?php if ( $is_in_wp_config ) : ?>disabled="disabled" <?php endif ?>/>
		<p class="description"><?php _e( "Ändere den Slug, der zum Erstellen von Dokumenten-URLs verwendet wird.", 'bp-docs' ) ?><?php if ( $is_in_wp_config ) : ?> <?php _e( 'Du hast diesen Wert bereits in <code>wp-config.php</code> definiert, sodass er hier nicht bearbeitet werden kann.', 'bp-docs' ) ?><?php endif ?></p>

		<?php
	}

	public function excerpt_length_setting_markup() {
		$length = bp_docs_get_excerpt_length();

		?>
		<label for="bp-docs-excerpt-length" class="screen-reader-text"><?php _e( "Ändere den Wert für längere oder kürzere Auszüge.", 'bp-docs' ) ?></label>
		<input name="bp-docs-excerpt-length" id="bp-docs-excerpt-length" type="text" value="<?php echo esc_html( $length ) ?>" />
		<p class="description"><?php _e( "Auszüge werden in Docs-Verzeichnissen angezeigt, um einen besseren Kontext zu bieten. Wenn Dein Theme oder Deinr Sprache längere oder kürzere Auszüge erfordert, ändere diesen Wert. Stelle <code>0</code> ein, um diese Auszüge zu deaktivieren.", 'bp-docs' ) ?></p>

		<?php
	}

	public function directory_title_setting_markup() {
		$label = bp_docs_get_docs_directory_title();

		?>
		<label for="bp-docs-directory-title" class="screen-reader-text"><?php _e( "Ändere den Titel des Hauptverzeichnisses \"Dokumente\".", 'bp-docs' ) ?></label>
		<input name="bp-docs-directory-title" id="bp-docs-directory-title" type="text" value="<?php echo $label; ?>" />
		<p class="description"><?php _e( "Ändere den Titel des Dokumenten-Hauptverzeichnisses von \"Dokumentenverzeichnis\" in \"Dokumentenverzeichnis\"&rsquo;d like.", 'bp-docs' ) ?></p>

		<?php
	}

	public function group_tab_name_setting_markup() {
		if ( ! bp_is_active( 'groups' ) ) {
			return;
		}
		$name = bp_docs_get_group_tab_name();

		?>
		<label for="bp-docs-tab-name" class="screen-reader-text"><?php _e( "Ändere das Wort auf der Registerkarte \"Dokumente\" der Gruppe.", 'bp-docs' ) ?></label>
		<input name="bp-docs-tab-name" id="bp-docs-tab-name" type="text" value="<?php echo esc_html( $name ) ?>" />
		<p class="description"><?php _e( "Ändere das Wort auf der Registerkarte \"BuddyPress-Gruppe\" von \"Dokumente\" nach Deinen Wünschen. Beachte, dass dadurch der Text an keiner anderen Stelle auf der Seite geändert wird. Erstelle für eine gründlichere Textänderung eine <a href='http://codex.buddypress.org/extending-buddypress/customizing-labels-messages-and-urls/'>Sprachdatei</a> für BP Collporate Docs.", 'bp-docs' ) ?></p>

		<?php
	}

	public function user_tab_name_setting_markup() {
		$name = bp_docs_get_user_tab_name();

		?>
		<label for="bp-docs-user-tab-name" class="screen-reader-text"><?php _e( "Ändere das Wort auf der Registerkarte \"Dokumente\" der Benutzer.", 'bp-docs' ) ?></label>
		<input name="bp-docs-user-tab-name" id="bp-docs-user-tab-name" type="text" value="<?php echo esc_html( $name ) ?>" />
		<p class="description"><?php _e( "Ändere das Wort auf den Registerkarten \"Dokumente\" der Benutzer von \"Dokumente\" in das gewünschte Wort. Beachte, dass dadurch der Text an keiner anderen Stelle auf der Seite geändert wird. Erstelle für eine gründlichere Textänderung eine <a href='http://codex.buddypress.org/extending-buddypress/customizing-labels-messages-and-urls/'>Sprachdatei</a> für BP Collporate Docs.", 'bp-docs' ) ?></p>

		<?php
	}

	public function enable_attachments_setting_markup() {
		$enabled = bp_docs_enable_attachments();

		?>
		<label for="bp-docs-enable-attachments" class="screen-reader-text"><?php _e( "Ermögliche Benutzern das Hinzufügen von Anhängen.", 'bp-docs' ) ?></label>
		<select name="bp-docs-enable-attachments" id="bp-docs-enable-attachments">
			<option value="yes" <?php selected( $enabled, true ) ?>><?php _e( 'Aktiviert', 'bp-docs' ) ?></option>
			<option value="no" <?php selected( $enabled, false ) ?>><?php _e( 'Deaktiviert', 'bp-docs' ) ?></option>
		</select>
		<p class="description"><?php _e( "Ermögliche Benutzern das Hinzufügen von Anhängen zu ihren Dokumenten.", 'bp-docs' ) ?></p>

		<?php
	}

	public function sanitize_slug( $value ) {
		$value = rawurlencode( $value );

		add_settings_error( 'bp-docs-settings', 'bp-docs-settings-saved', __( 'Einstellungen aktualisiert.', 'bp-docs' ), 'updated' );

		return $value;
	}

	function replace_recent_comments_dashboard_widget() {
		global $wp_meta_boxes;

		// Find the recent comments widget
		foreach ( $wp_meta_boxes['dashboard'] as $context => $widgets ) {
			if ( !empty( $widgets ) && !empty( $widgets['core'] ) && is_array( $widgets['core'] ) && array_key_exists( 'dashboard_recent_comments', $widgets['core'] ) ) {
				// Take note of the context for when we add our widget
				$drc_widget_context = $context;

				// Store the widget so that we have access to its information
				$drc_widget = $widgets['core']['dashboard_recent_comments'];

				// Store the array keys, so that we can reorder things later
				$widget_order = array_keys( $widgets['core'] );

				// Remove the core widget
				remove_meta_box( 'dashboard_recent_comments', 'dashboard', $drc_widget_context );

				// No need to continue the loop
				break;
			}
		}

		// If we couldn't find the recent comments widget, it must have been removed. We'll
		// assume this means we shouldn't add our own
		if ( empty( $drc_widget ) )
			return;

		// Set up and add our widget
		$recent_comments_title = __( 'Letzte Kommentare', 'bp-docs' );

		// Add our widget in the same location
		wp_add_dashboard_widget( 'dashboard_recent_comments_bp_docs', $recent_comments_title, array( $this, 'wp_dashboard_recent_comments' ), 'wp_dashboard_recent_comments_control' );

		// Restore the previous widget order. File this under "good citizenship"
		$wp_meta_boxes['dashboard'][$context]['core']['dashboard_recent_comments'] = $wp_meta_boxes['dashboard'][$context]['core']['dashboard_recent_comments_bp_docs'];

		unset( $wp_meta_boxes['dashboard'][$context]['core']['dashboard_recent_comments_bp_docs'] );

		// In order to inherit the styles, we're going to spoof the widget ID. Sadness
		$wp_meta_boxes['dashboard'][$context]['core']['dashboard_recent_comments']['id'] = 'dashboard_recent_comments';
	}

	/**
	 * Replicates WP's native recent comments dashboard widget.
	 *
	 * @since 1.1.8
	 */
	function wp_dashboard_recent_comments() {
		global $wpdb, $bp;

		if ( current_user_can('edit_posts') )
			$allowed_states = array('0', '1');
		else
			$allowed_states = array('1');

		// Select all comment types and filter out spam later for better query performance.
		$comments = array();
		$start = 0;

		$widgets = get_option( 'dashboard_widget_options' );
		$total_items = isset( $widgets['dashboard_recent_comments'] ) && isset( $widgets['dashboard_recent_comments']['items'] )
			? absint( $widgets['dashboard_recent_comments']['items'] ) : 5;

		while ( count( $comments ) < $total_items && $possible = $wpdb->get_results( "SELECT c.*, p.post_type AS comment_post_post_type FROM $wpdb->comments c LEFT JOIN $wpdb->posts p ON c.comment_post_ID = p.ID WHERE p.post_status != 'trash' ORDER BY c.comment_date_gmt DESC LIMIT $start, 50" ) ) {

			foreach ( $possible as $comment ) {
				if ( count( $comments ) >= $total_items )
					break;

				// Is the user allowed to read this doc?
				if ( $bp->bp_docs->post_type_name == $comment->comment_post_post_type && !bp_docs_user_can( 'read', get_current_user_ID(), $comment->comment_post_ID ) )
					continue;

				if ( in_array( $comment->comment_approved, $allowed_states ) && current_user_can( 'read_post', $comment->comment_post_ID ) )
					$comments[] = $comment;
			}

			$start = $start + 50;
		}

		if ( $comments ) :
	?>

			<div id="the-comment-list" class="list:comment">
	<?php
			foreach ( $comments as $comment )
				_wp_dashboard_recent_comments_row( $comment );
	?>

			</div>

	<?php
			if ( current_user_can('edit_posts') ) { ?>
				<?php _get_list_table('WP_Comments_List_Table')->views(); ?>
	<?php	}

			wp_comment_reply( -1, false, 'dashboard', false );
			wp_comment_trashnotice();

		else :
	?>

		<p><?php _e( 'Noch keine Kommentare.', 'bp-docs' ); ?></p>

	<?php
		endif; // $comments;
	}
}
$bp_docs_admin = new BP_Docs_Admin;
