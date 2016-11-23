<?php
/*
Plugin Name: SP Wordpress Concat JS
Plugin URI: https://github.com/miloszryckobozenski
Description: Concats scripts from footer into one file
Version: 20161123
Author: Milosz Rycko-Bozenski Stukot Pikseli
Author URI: http://stukotpikseli.pl
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: spwpconcatjs
*/

require plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

define( 'TEMPLATE', get_template_directory_uri() );

if( ! is_admin() ) {
	global $wp_scripts;

	$scripts_collection = array();

	foreach( $wp_scripts->in_footer as $script_name ) {
		if( $script_name != 'admin-bar' ) {
			$registered = $wp_scripts->registered;
			$scripts_collection = new Assetic\Asset\FileAsset( $registered[ $script_name ]->src );
			wp_dequeue_script( $script_name );
		}
	}

	$js = new Assetic\Asset\AssetCollection( $scripts_collection, array(
		new Assetic\Filter\GoogleClosure\CompilerApiFilter()
	) );

	$output_js = TEMPLATE . '/cache/scripts.min.js';
	$cachetime = 3600;
	$created = ( ( @file_exists( $output_js ) ) ) ? @filemtime( $output_js ) : 0;

	if( ( $created - ( time() - $cachetime ) ) < 0 ) {
		file_put_contents( $output_js, $js->dump() );
	}

	wp_enqueue_script( 'scripts-mini', TEMPLATE . '/cache/scripts.min.js', '', '', true );
}

?>