[![Build Status](https://travis-ci.org/CreITive/Laravel-4-Breadcrumbs.png)](https://travis-ci.org/CreITive/Laravel-4-Breadcrumbs) [![Latest Stable Version](https://poser.pugx.org/creitive/breadcrumbs/version.png)](https://packagist.org/packages/creitive/breadcrumbs) [![Total Downloads](https://poser.pugx.org/creitive/breadcrumbs/d/total.png)](https://packagist.org/packages/creitive/breadcrumbs) [![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/CreITive/laravel-4-breadcrumbs/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

Laravel 4 Breadcrumbs
=====================

*A simple Laravel 4-compatible breadcrumbs package. Generates Twitter Bootstrap-compatible output.*


Installation
------------

### composer.json (recommended for Laravel 4, and any other framework using the same IoC dependency injection as L4)

Just add the package to your `composer.json` require section:

```js
{
	"require": {
		// ...
		"creitive/breadcrumbs": "dev-master"
	}
	// ...
}
```

After that, run `composer install`.

Alternatively, you may add it through the command line:

```
composer require creitive/breadcrumbs dev-master
```

If this is not done automatically (which depends on your `composer.json` settings), you should also run `composer dump-autoload`.

After this, you should add the service provider and the alias to your `app/config/app.php`, which should make it look something like this:

```php
return array(
	// ...

	'providers' => array(
		// ...

		'Creitive\Breadcrumbs\BreadcrumbsServiceProvider',
	),

	// ...

	'aliases' => array(
		// ...

		'Breadcrumbs' => 'Creitive\Breadcrumbs\Facades\Breadcrumbs',
	),
);
```

You're all set!


### Manual installation

This class is usable on its own as well, without the service provider/facade pattern used in Laravel 4. Just take the `src/Creitive/Breadcrumbs/Breadcrumbs.php` file, place it somewhere in your project where it will be autoloaded (or even `require_once` the file, if you're that raw), instantiate it, and use it:

```php
$breadcrumbs = new Creitive\Breadcrumbs\Breadcrumbs();

$breadcrumbs->addCrumb('Home', '/');

echo $breadcrumbs;
```


Usage
-----

The usage manual is described for Laravel 4, using the provided facade - if you wish to use the class standalone, just call these methods on an instance (ie. instead of `Breadcrumbs::addCrumb()`, use `$breadcrumbs->addCrumb()`).

### Adding a crumb

```php
Breadcrumbs::addCrumb('Home', '/');
```

The first argument is the title of the crumb, and the second one is that crumb's address. Two different href-forming models exist: if this argument begins with a forward slash, it will be treated as a root URL, and the corresponding breadcrumb will link to it as entered. If it does *not* begin with a slash, it will be treated as a segment appended to its previous breadcrumb.

#### Example

```php
Breadcrumbs::addCrumb('Home', '/');
Breadcrumbs::addCrumb('Pages', 'pages');
Breadcrumbs::addCrumb('Subpage', 'subpage');
Breadcrumbs::addCrumb('Subsubpage', '/subsubpage');

echo Breadcrumbs::render();
```

The third breadcrumb ("Subpage") will link to `/pages/subpage`, building on the previous breadcrumb. However, the fourth and last breadcrumb will link to `/subsubpage`, because its address starts with a slash.


### CSS classes

Three CSS class manipulation methods are at your disposal, each working more or less similarly:

```php
Breadcrumbs::setCssClasses($classes);
Breadcrumbs::addCssClasses($classes);
Breadcrumbs::removeCssClasses($classes);
```

Each of these methods manipulates the classes which will be applied to the containing `<ul>` element. All of them may be passed either a string or an array. If passed a string, separate CSS classes should be separated with spaces.


```php
$stringClasses = 'class1 class2 class3';
$arrayClasses = array('class4', 'class5');

Breadcrumbs::addCssClasses($stringClasses);
Breadcrumbs::addCssClasses($arrayClasses);

// All five classes will now be applied to the containing `<ul>` element.
```


### Divider

The default breadcrumb divider is `/`, which is the default used by Twitter Bootstrap. If you'd like to change it to, for example, `»`, you can just do:

```php
Breadcrumbs::setDivider('»');
```

The dividers are rendered as `<span>`s with the `divider` CSS class. If you would like to use the new Bootstrap 3 HTML (which uses `::before` CSS pseudo-element to style the separators), just set the divider to an empty string or `null`, and those elements will not be rendered at all in the HTML.


### Output

Finally, when you actually want to display your breadcrumbs, all you need to do is call the `render()` method from the facade:

```php
echo Breadcrumbs::render();
```

The class also implements the `__toString()` magic method, so if you're using the class manually, without the facade, you can just do this:

```php
echo $breadcrumbs;
```

Note that crumbs are rendered without escaping HTML characters, which was designed for flexibility, allowing you to use, say, `<img>` elements as breadcrumbs, which means that you should escape any text-content yourself when adding crumbs.


License
-------

The code is licensed under the MIT license, which is available in the `LICENSE` file.
