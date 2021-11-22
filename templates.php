<?php

use Underpin\Abstracts\Underpin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
Underpin::attach( 'setup', new \Underpin\Factories\Observer( 'templates', [
	'update' => function ( Underpin $plugin, $args ) {
		require_once( plugin_dir_path( __FILE__ ) . 'lib/abstracts/Template.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/factories/Template_Instance.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'lib/loaders/Templates.php' );

		$plugin->loaders()->add( 'templates', [
			'class' => 'Underpin_Templates\Loaders\Templates',
		] );
	},
] ) );