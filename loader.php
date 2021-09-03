<?php
/*
Plugin Name: BP Kollaborative Docs
Plugin URI: http://github.com/boonebgorges/bp-docs
Description: BuddyPress Docs Psource-Edition, fügt BuddyPress kollaborative Dokumente hinzu, behebt alle Probleme welche BuddyPress Docs mit Deutschen Übersetzungsfiles immer hatte.
Version: 2.6.5
Author: DerN3rd
Author URI: https://n3rds.work
Text Domain: bp-docs
Domain Path: /languages/
Licence: GPLv3
*/

/*
It's on like Donkey Kong
*/
require 'includes/psource-plugin-update/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://n3rds.work//wp-update-server/?action=get_metadata&slug=bp-docs', 
	__FILE__, 
	'bp-docs' 
);
define( 'BP_DOCS_VERSION', '2.6.5' );

/*
 * BP Dokumente introduces a lot of overhead. Unless otherwise specified,
 * don't load the plugin on subsites of an MS install
 */
if ( ! defined( 'BP_DOCS_LOAD_ON_NON_ROOT_BLOG' ) ) {
	define( 'BP_DOCS_LOAD_ON_NON_ROOT_BLOG', false );
}

/**
 * Loads BP Docs files only if BuddyPress is present
 *
 * @package BP Dokumente
 * @since 1.0-beta
 */
function bp_docs_init() {
	global $bp_docs;

	if ( is_multisite() && ! bp_is_root_blog() && ( ! BP_DOCS_LOAD_ON_NON_ROOT_BLOG ) ) {
		return;
	}

	require dirname( __FILE__ ) . '/bp-docs.php';
	$bp_docs = new BP_Docs();
}
add_action( 'bp_include', 'bp_docs_init' );
