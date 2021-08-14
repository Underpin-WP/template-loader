<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
add_action( 'underpin/before_setup', function ( $file, $class ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/abstracts/Template.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/factories/Template_Instance.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'lib/loaders/Templates.php' );

	Underpin\underpin()->get( $file, $class )->loaders()->add( 'templates', [
		'registry' => 'Underpin_Templates\Loaders\Templates',
	] );
}, 10, 2 );