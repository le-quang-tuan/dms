# Laravel Flash Messages with jGrowl

[![Latest Version on Packagist](https://img.shields.io/packagist/v/samkitano/laragrowl.svg?style=flat-square)](https://packagist.org/packages/samkitano/laragrowl)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/samkitano/laragrowl/master.svg?style=flat-square)](https://travis-ci.org/samkitano/laragrowl)
[![Total Downloads](https://img.shields.io/packagist/dt/samkitano/laragrowl.svg?style=flat-square)](https://packagist.org/packages/samkitano/laragrowl)

All the awesomeness and functionality of [jGrowl](https://github.com/stanlemon/jGrowl) made simple, for your Laravel project.

**jGrowl** is "an unobtrusive notification system for web applications", by [Stan Lemon](https://github.com/stanlemon). If you are not acquainted with this marvelous notification system, I encourage you to [pay a visit](https://github.com/stanlemon/jGrowl) and read the documentation.

I find this notification system to be *primus inter pares*. Very well documented and mantained, "unobtrusive" as advertised, lightweight, flexible, and easy to use. Since I constantly use it, particularly in the Admin sections of my projects, I wrote **Laragrowl** to easily include it in my Laravel apps.

The main goal of **Laragrowl**, rather than fully exploit **jGrowl**'s capabilities, is to provide an out-of-the-box, easy-to-use, consistent notification system within Laravel. I believe this is fully achieved with single-liners like `Laragrowl::success('Your model has been updated');`.

Furthermore, with Laravel's awesome ability to use Aliases for the facades, you can even "rename" **Laragrowl** into something like **Notify** or **Inform**. I.e. `Inform::error('Validation Failed')`. Whatever suits your taste.

## Requirements

	-Laravel >= 5.2.X
	-PHP >= 5.6
	-jQuery >= 1.9.0

## Installation

1 - Require with Composer: `composer require samkitano/laragrowl`

2 - Include the service provider in the 'providers' array within `config/app.php`.

```php
'providers' => [
    Kitano\Laragrowl\LaragrowlServiceProvider::class,
];
```

3 - Scroll down `config/app.php` and add an alias of your convenience (i.e. 'Notify') for the facade:

```php
'aliases' => [
    'Notify' => Kitano\Laragrowl\Laragrowl::class,
];
```

3 - At this point, you must decide whether to **a)** download jGrowl; **b)** use a [CDN](https://cdnjs.com/libraries/jquery-jgrowl); or **c)** publish the included version of jGrowl (1.4.5)

**a)** Download jGrowl, copy the files to a directory of your convenience and include them in your template:

```php
<!DOCTYPE html>
<html>
    <head>
        ...
        <link href="path_to_your_downloaded_jGrowl_css_files" rel="stylesheet" type="text/css"/>
        ...
    </head>

    <body>
        ...
        <!-- Place jQuery Here -->
        <script src="path_to_your_downloaded_jGrowl_js_files"></script>
        ...
    </body>
</html>
```

**b)** Using a CDN:

```php
<!DOCTYPE html>
<html>
    <head>
      ...
      <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.4.1/jquery.jgrowl.min.css" />
	  <!-- Place Your Theme Here -->
	  ...
    </head>

    <body>
        ...
        <!-- Place jQuery Here -->
        <script src="https//cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.4.1/jquery.jgrowl.min.js"></script>
        ...
    </body>
</html>
```

If you decide to **download**, or to use a **CDN**, you'll have to provide your own styling for the notifications. A basic theme is included in this package in `src\assets\css\jquery.jgrowl.theme.css` Feel free to use it "as is", or as a boilerplate to suit your own needs.

**c)** Publish included **jGrowl** version:

```bash
php artisan vendor:publish --provider="Kitano\Laragrowl\LaragrowlServiceProvider" --tag=assets
```

4 - Include the CSS files within your master template `<head>` section, and the core jGrowl script file right before the closing `</body>` tag.

```php
<!DOCTYPE html>
<html>
    <head>
        <title>Laragrowl</title>
        <link href="{{ url('vendor/css/jquery.jgrowl.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ url('vendor/css/jquery.jgrowl.theme.min.css') }}" rel="stylesheet" type="text/css">
    <head>

    <body>
        <script src="your.jquery.version.js"></script>
        <script src="{{ url('vendor/js/jquery.jgrowl.min.js') }}"></script>
    </body>

</html>
```

5 - Publish the message view:

```bash
php artisan vendor:publish --provider="Kitano\Laragrowl\LaragrowlServiceProvider" --tag=views
```

6 - Include the view after the jGrowl `<script>` tag in your master template:

```php
	<body>
		<script src="your.jquery.version"></script>
		<script src="{{ url('vendor/js/jquery.jgrowl.min.js) }}"></script>

		@include('vendor.laragrowl.message')
	</body>

```

**REMEMBER:** jGrowl is a jQuery plugin, so you MUST have jQuery loaded **before** your jGrowl source.

If you are comfortable with elixir, and/or you like to master your own assets, you know what to do next.

By all means, feel free to modify the provided view `resources\vendor\laragrowl\message.blade.php` to fit your own requirements.

## Usage

*Where?* Where you'd usually place a `Session::flash('important message')`.

*How?* You can either use the facade `Laragrowl::message` or the alias you have provided (if so):

```php
// Examples
public function store()
{
	...

	// PICK YOUR FLAVOUR

	// With an Alias, like "Notify"
	Notify::message('Your stuff has been stored.', 'success');

	// With the Facade
	Laragrowl::message('Your stuff has been stored', 'success');

	// Using available methods to declare the message type
	Laragrowl::default('Your stuff has been stored');
	Laragrowl::success('Your stuff has been stored');
	Laragrowl::error('Your stuff was NOT stored');
	Laragrowl::warning('Your form contains errors, check fields');
	Laragrowl::info('You have a neww message');

	...
}
```

### Methods

Method | Arguments | Description | Example |
-------|-----------|-------------|----------------------|
::message | `message`, `type`, `header`, `sticky`, `life`, `group` | Displays a notification according to parameters. If `type` is not provided, theme will be "default". | Laragrowl::message('Welcome, User!', 'default', 'HI THERE')
::default| `message`, `header`, `sticky` | Displays a "default" themed notification. | Laragrowl::default('Welcome, User!');
::info | `message`, `header`, `sticky` | Displays an "info" themed notification. Ideal for quickly provide an informational message. | Laragrowl::info('Please fill out this form')
::success | `message`, `header`, `sticky` | Displays a "success" themed notification. Ideal for quickly provide a success message. | Laragrowl::success('Data Saved!', 'SUCCESS!')
::warning | `message`, `header`, `sticky` | Displays a "warning" themed notification. Ideal for quickly provide a warning message. | Laragrowl::warning('FYI: Third party service is not available at this time.', 'HEY!')
::error | `message`, `header`, `sticky` | Displays an "error" themed notification. Ideal for quickly provide an error message. | Laragrowl::error('Validation Failed! Please correct form erros', 'ERROR', 'sticky')

### Arguments

Argument | Type |Default value | Description |
---------|:----------:|:------:|:--------------|
`message` | String | | The message to display.
`type` | String | 'success' | The default theme for messages. Available: 'default', 'info', 'success', 'warning' and 'error'.
`header` | String | '' | The message prefix, i.e. 'Error'.
`sticky` | Boolean or String | False | Weather or not the message will display forever until closed.
`life` | Integer or String | 6000 | Lifetime of a non-sticky message, in milisseconds.
`group` | String | '' | An extra CSS selector useful to group messages.

---

## License

Laragrowl is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
