# Underpin Template Loader

Loader That assists with registering templates to themes.

**NOTE: If you're building a WordPress plugin, you probably don't need this, you probably just need
the [Template Trait](github.com/underpin-WP/underpin#template-system-trait) built directly into Underpin.**

This loader makes it possible to create custom templates to be used arbitrarily in your theme. It
replaces `get_template_part`, and adds some useful beneftis:

1. It provides a clear place to put logic, data-fetching, and other things of the sort.
2. Templates can be nested.
3. Templates can selectively be overridden, and extended by the child theme

## Installation

### Using Composer

`composer require underpin/template-loader`

### Manually

This plugin uses a built-in autoloader, so as long as it is required _before_ Underpin, it should work as-expected.

`require_once(__DIR__ . '/underpin-templates/templates.php');`

## Setup

1. Install Underpin. See [Underpin Docs](https://www.github.com/underpin-wp/underpin)
1. Register new templates as-needed.

### Registering a Template

Like any loader item, Templates must be registered. This can be done in a few different ways.

```php
underpin()->templates()->add( 'index', [
	'description' => "Renders the home page.", // Human-readable description
	'name'        => "Index Template.",        // Human-readable name
	'group'       => 'index',                  // Template group.
	'root_path'   => underpin()->template_dir()// Template path
	'templates'   => [                         // Templates to include.
		'loop'     => 'public',
		'post'     => 'public',
		'no-posts' => 'public',
	],
] );
```

The above example would expect three templates inside the `templates/index` directory.

1. `loop`
1. `post`
1. `no-posts`

This is a great down-and dirty way to set up a template, however some more-complex templates will benefit from using
custom class methods inside the template. In these cases, it makes more sense to extend `Theme\Abstracts\Template` so
you can add your own logic and keep your markup clean.

You can register a template as a class directly, like so:

```php
// lib/templates/Index.php

class Index extends \Theme\Abstracts\Template{

  protected $name = 'Index Template';
  
  protected $description = 'Human Read-able description';

  protected $group = 'index';

  protected $templates = [                         // Templates to include.
		'loop'     => 'public',
		'post'     => 'public',
		'no-posts' => 'public',
	];

  // Optionally place any helper methods specific to this template here. These methods would be use-able inside of the
  // template, and can really help keep your templates clean.
}
```

And then register your template in `functions.php` like so:

```php
underpin()->templates()->add('index','Theme\Templates\Index');
```

### Rendering Template Output

The purpose of a template is to render the output HTML. Ideally, all of your logic would be pre-determined, and passed
directly to your template so it can be accessed directly via `get_param()`.

Let's take a look at the basic WordPress loop using the template system:

```php
<?php
/**
 * Index Loop Template
 *
 * @author: Alex Standiford
 * @date  : 12/21/19
 * @var Theme\Abstracts\Template $template
 */

// This confirms that nobody is trying to be cute, and load this template in a potentially dangerous way.
if ( ! underpin()->templates()->is_valid_template( $template ) ) {
	return;
}

?>
<main>
	<?php if ( have_posts() ): ?>
		<?php while ( have_posts() ): the_post(); ?>
			<?= $template->get_template( 'post' ); ?>
		<?php endwhile; ?>
	<?php else: ?>
		<?= $template->get_template( 'no-posts' ); ?>
	<?php endif; ?>
	<?php get_sidebar(); ?>
</main>
```

Notice how we're referencing `$template` as-if it's a class? That's because it's _literally_ the instance of the
Template class. You can reference it directly as `$template`. This means you can use any of the methods inside your
Template class.

This includes rendering sub-templates by running `get_template`. In this context, you don't need to specify the group
because, `$template` already knows the group - you just need to tell it which template to use in the group.

Instead, the second argument for `get_template` is an optional associative array of arguments that get passed to the
next template. Those args can be accessed using `$template->get_param('argument-key', 'fallback_value')`

### Calling a template

To call a template, you can do this:

```php
<?= underpin()->templates()->get_template( 'index', 'loop', [/* Arguments to pass to template */] ); ?>
```

where `index` is your template group, and `loop` is the template you wish to load. The third argument is an associative
array of arguments that you can pass to the template.

Just like inside the `$template` context above, arbitrary data that is passed to a template can be accessed using
`$template->get_param('key', 'fallback value')` where the first argument is the array key to grab, and the second value
is a default value to display if the key is not set.

You can learn more about the template system
in [Underpin's docs](https://github.com/Underpin-WP/underpin/#template-system-trait).

## Extending Templates in Child Themes

Extending this boilerplate in a child theme works exactly like extending anything else in Underpin - by hooking in at
the right time, and registering the custom items as-necessary.

### Overriding Theme Templates

Any template registered in the parent theme can be overridden by matching the directory where the template is placed
inside the child theme. For example, if you wanted to override the entire header, you could create a file from your
child theme's root: `templates/header/header.php`. The template system will use this template instead of what's
specified in the parent theme.

### Extending The Theme

You can modify anything that is registered in the parent theme from the child theme by hooking
into `underpn/after_setup`. This is a great place to register custom stylesheets, scripts, or templates.

This would go in your child theme's `functions.php` file.

```php

// Hook into Underpin's after_setup hook
add_action( 'underpin/after_setup', function ( $file, $class ) {
    
    // If the file is the parent theme, register the things. Ensures these only register one-time
	if ( trailingslashit( dirname( $file ) ) === trailingslashit( get_template_directory() ) ) {
	  // Do things
	}

}
```

### Extend Templates

If you're working with a child theme, you can register custom templates to existing groups by hooking just after the
theme is set up, like so:

```php

// Hook into Underpin's after_setup hook
add_action( 'underpin/after_setup', function ( $file, $class ) {
    
    // If the file is the parent theme, register the things. Ensures these only register one-time
	if ( trailingslashit( dirname( $file ) ) === trailingslashit( get_template_directory() ) ) {
	  // Add a new template group, with custom templates
	  theme()->templates()->add('custom-template-group', [/** Arguments to register child theme-specific template **/]);
	  
	  // Add a new template inside an existing group. This example extends the footer to include a slogan
	  theme()->templates()->get( 'footer' )->add_template( 'slogan',['override_visibility' => 'public'] );
	}

}
```
