<?php
/**
 *
 *
 * @since
 * @package
 */

namespace Underpin\Templates\Abstracts;

use Underpin\Traits\Templates;
use Underpin\Factories\Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Template
 *
 *
 * @since
 * @package
 */
abstract class Template {

	use Templates;

	protected $templates = [];
	protected $group     = '';
	protected $root_path = '';

	public function __construct() {

		// Convert templates into a registry
		$this->templates = new Registry( [
			'registry_id'            => $this->group . '_templates',
			'name'                   => $this->group . ' Templates',
			'description'            => 'Templates used by the ' . $this->group . '.',
			'default_items'          => $this->templates,
			'validate_callback'      => '__return_true' // skip validation
		] );
	}

	/**
	 * Adds a new use-able template to this group.
	 *
	 *
	 * @param string $key The template key
	 * @param        $args
	 */
	public function add_template( $key, $args ) {
		$this->templates->add( $key, $args );
	}

	public function get_templates() {
		return (array) $this->templates;
	}

	protected function get_template_group() {
		return $this->group;
	}

	public function get_template_root_path() {
		return $this->root_path;
	}
}