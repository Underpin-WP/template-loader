<?php

use Underpin\Abstracts\Underpin;
use Underpin\Factories\Observers\Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
Underpin::attach( 'setup', new Loader('templates', [ 'class' => 'Underpin\Templates\Loaders\Templates' ] ) );