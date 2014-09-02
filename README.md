# reveal.js for WordPress

Power your reveal.js presentation with WordPress

## Installation Instructions

1. Check this out in your wp-content/themes/ directory
2. Activate it as you would any other theme
3. Install the [Fieldmanager](https://github.com/alleyinteractive/wordpress-fieldmanager) plugin and activate it
4. Customize your presentation in **Appearance &rarr; reveal.js Settings**
5. Start making slides!

## Customizing the theme

This theme can be used as a parent theme so you can customize your presentation without having to fork it.
To do so, simply create a [new child theme](http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
and set this one as the parent. There are a number of actions and filters to customize the theme:

### reveal.js Styling

To change the reveal.js theme (not to be confused with the WordPress Theme), there is a settings page under
**Appearance &rarr; reveal.js Settings**. To add your own theme, You can filter `reveal_theme_url` to use a
custom theme URL.

### Actions

* `reveal_before_body`: Fires immediately after the opening `<body>` tag.
* `reveal_before_slides_wrapper`: Fires immediately after the opening `div.reveal` tag.
* `reveal_before_slides`: Fires immediately after the opening `div.slides` tag.
* `reveal_after_slides`: Fires immediately before the closing `div.slides` tag.
* `reveal_after_slides_wrapper`: Fires immediately before the closing `div.reveal` tag.
* `reveal_initialize`: Fires before `Reveal.initialize()` is called. This theme defines a
	javascript variable `reveal_config` which is the object passed to `Reveal.initialize()`.
	Using this variable, you can manipualte any config value. For instance:

	```php
	add_action( 'reveal_initialize', function(){
		?>
		reveal_config.center = false;
		reveal_config.transition = 'concave;
		<?php
	} );
	```

	However, you can change most of the settings in **Appearance &rarr; reveal.js Settings**.

### Filters

* `reveal_theme_url`: The URL to the theme CSS you'd like to use
* `reveal_default_dependencies`: You can manipulate the reveal dependencies by modifying
	`reveal_config.dependencies` using the `reveal_initialize` action, but this filter is a
	bit easier. This filter passes the following array, which you can manipualte as needed:

	```php
	array(
		'classList' => "{ src: '" . get_template_directory_uri() . "/lib/js/classList.js', condition: function() { return !document.body.classList; } }",
		'highlight' => "{ src: '" . get_template_directory_uri() . "/plugin/highlight/highlight.js', async: true, callback: function() { hljs.initHighlightingOnLoad(); } }",
		'zoom'      => "{ src: '" . get_template_directory_uri() . "/plugin/zoom-js/zoom.js', async: true, condition: function() { return !!document.body.classList; } }",
		'notes'     => "{ src: '" . get_template_directory_uri() . "/plugin/notes/notes.js', async: true, condition: function() { return !!document.body.classList; } }",
	)
	```
* `reveal_default_settings`: Manipulate the default settings.
See `reveal_default_settings()` functions.php for the values.


### Other Customizations

Using a child theme, your options for customization are limitless. Everything in the parent theme can
be disabled or overridden. Have fun, and let us know what you do with this!


## What is reveal.js?

A framework for easily creating beautiful presentations using HTML. [Check out the live demo](http://lab.hakim.se/reveal-js/).

#### More reading:
- [reveal.js on GitHub](https://github.com/hakimel/reveal.js/)
- [reveal.js Examples](https://github.com/hakimel/reveal.js/wiki/Example-Presentations): Presentations created with reveal.js, add your own!
- [reveal.js Browser Support](https://github.com/hakimel/reveal.js/wiki/Browser-Support): Explanation of browser support and fallbacks.


## License

This theme, like WordPress, is licensed under the GPL.
Use it to make something cool, have fun, and share what you've learned with others.

reveal.js is Copyright (C) 2014 Hakim El Hattab, http://hakim.se and is licensed under the "MIT license".
