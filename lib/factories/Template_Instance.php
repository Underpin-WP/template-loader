<?php

namespace Underpin_Templates\Factories;

use Underpin_Templates\Abstracts\Template;
use Underpin\Traits\Instance_Setter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Template_Instance extends Template {
	use Instance_Setter;

	public function __construct( $args ) {
		$this->set_values( $args );
		parent::__construct();
	}
}