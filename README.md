[![Build Status](https://travis-ci.org/creitive/breadcrumbs.png)](https://travis-ci.org/creitive/breadcrumbs) [![Latest Stable Version](https://poser.pugx.org/creitive/breadcrumbs/version.png)](https://packagist.org/packages/creitive/breadcrumbs) [![Total Downloads](https://poser.pugx.org/creitive/breadcrumbs/d/total.png)](https://packagist.org/packages/creitive/breadcrumbs)

> ## Note on Laravel support
>
> This repository was originally created as a package for Laravel 4, and included a service provider and a facade. Since that time, Laravel 5 came out, which uses slightly different techniques to handle packages. We've contemplated hacking something together to include support for both Laravel 4 and 5 in one package, or having separate branches for that, but in the end, opted for releasing a new major version of this package, `2.0.0`, that doesn't depend on Laravel at all, and instead focuses on the core breadcrumbs functionality.
>
> For Laravel support use one of the following packages:
>
> - Laravel 4: [creitive/laravel4-breadcrumbs](https://github.com/creitive/laravel4-breadcrumbs)
> - Laravel 5: [creitive/laravel5-breadcrumbs](https://github.com/creitive/laravel5-breadcrumbs)
>
> This was done so as not to break backwards-compatibility for anyone - users with Laravel 4 apps can still depend on `creitive/breadcrumbs:~1.0`, and their apps will continue to work. However, the `1.0` version will receive no further updates, because the upgrade process should be real easy, and we urge everyone to do so:
>
> ```sh
> composer remove creitive/breadcrumbs
> composer require creitive/laravel4-breadcrumbs
> ```
>
> This package reorganization also provides for a proper separation of concerns between packages, and enables others to make their own framework-specific integration packages, if they wish to do so.


Breadcrumbs
===========

*A simple breadcrumbs package. Generates Twitter Bootstrap-compatible output.*


Installation
------------

Just run this on the command line:

```
composer require creitive/breadcrumbs
```


Usage
-----

The basic idea is to create an instance of the `Creitive\Breadcrumbs\Breadcrumbs` class somewhere in your app, and add crumbs using the provided methods described below. At a later time, you can just render the output somewhere (likely in a view, depending on your app infrastructure), which will generate Twitter Bootstrap-compatible HTML.

```php
$breadcrumbs = new Creitive\Breadcrumbs\Breadcrumbs;

$breadcrumbs->addCrumb('Home', '/');

echo $breadcrumbs->render();
```

The rest of the documentation will assume you have a `$breadcrumbs` instance on which you are making calls.


### Adding a crumb

```php
$breadcrumbs->addCrumb('Home', '/');
```

The first argument is the title of the crumb, and the second one is that crumb's address. There are a few ways you can pass the address argument - if this argument begins with a forward slash, or a protocol (`http`/`https`), it will be treated as a complete URL, and the corresponding breadcrumb will link to it as-is. If it does *not* begin with either of those, it will be treated as a segment, and it will be appended to its previous breadcrumb.

> **Note**: You can use also use `add`, which is an alias of `addCrumb`:
>
> ```php
> $breadcrumbs->add('Home', '/');
> ```
>
> In fact, method chaining is supported wherever possible, ie. all methods that aren't expected to have a specific return value: `setBreadcrumbs()`, `addCrumb()`, `add()`, `setCssClasses()`, `addCssClasses()`, `removeCssClasses()`, `setDivider()`, `setListElement()`, `removeAll()`.


#### Example

```php
$breadcrumbs->addCrumb('Home', '/');
$breadcrumbs->addCrumb('Pages', 'pages');
$breadcrumbs->addCrumb('Subpage', 'subpage');
$breadcrumbs->addCrumb('Subsubpage', '/subsubpage');
$breadcrumbs->addCrumb('Other website', 'http://otherwebsite.com/some-page');

echo $breadcrumbs->render();
```

The third breadcrumb ("Subpage") will link to `/pages/subpage`, building on the previous breadcrumb. However, the fourth breadcrumb will link to `/subsubpage`, because its address starts with a slash. The last breadcrumb will obviously link to the passed URL.

You can also chain calls to `addCrumb` in order to add multiple crumbs at once:

```php
$breadcrumbs->addCrumb('Home', '/')
    ->addCrumb('Pages', 'pages')
    ->addCrumb('Subpage', 'subpage')
    ->addCrumb('Subsubpage', '/subsubpage')
    ->addCrumb('Other website', 'http://otherwebsite.com/some-page');
```

### CSS classes

Three CSS class manipulation methods are at your disposal, each working more or less similarly:

```php
$breadcrumbs->setCssClasses($classes);
$breadcrumbs->addCssClasses($classes);
$breadcrumbs->removeCssClasses($classes);
```

Each of these methods manipulates the classes which will be applied to the containing `<ul>` element. All of them may be passed either a string or an array. If passed a string, separate CSS classes should be separated with spaces.


```php
$stringClasses = 'class1 class2 class3';
$arrayClasses = array('class4', 'class5');

$breadcrumbs->addCssClasses($stringClasses);
$breadcrumbs->addCssClasses($arrayClasses);

// All five classes will now be applied to the containing `<ul>` element.
```


### Divider

The default breadcrumb divider is `/`, which is the default used by Twitter Bootstrap. If you'd like to change it to, for example, `»`, you can just do:

```php
$breadcrumbs->setDivider('»');
```

The dividers are rendered as `<span>`s with the `divider` CSS class. If you would like to use the new Bootstrap 3 HTML (which uses `::before` CSS pseudo-element to style the separators), just set the divider to an empty string or `null`, and those elements will not be rendered at all in the HTML.


### The List Element

The default list element used to wrap the breadcrumbs, is `ul`. To change it, use the setListElement method like so:

```php
$breadcrumbs->setListElement('ol');
```


### Output

Finally, when you actually want to display your breadcrumbs, all you need to do is call the `render()` method on the instance:

```php
echo $breadcrumbs->render();
```

> **Note:** You can also just echo the instance like this:
>
> ```php
> echo $breadcrumbs;
> ```

Note that crumb titles are rendered without escaping HTML characters, which was designed for flexibility, allowing you to use, say, `<img>` elements as breadcrumbs. This means that you should escape any text-content yourself when adding crumbs:

```php
$breadcrumbs->addCrumb('<img src="/images/foo.png">', '/foo');
$breadcrumbs->addCrumb(htmlspecialchars($userSubmittedName), 'bar');
```

The output contains microdata markup - see Google's [Structured Data documentation on breadcrumbs](https://developers.google.com/structured-data/breadcrumbs) for more info.


Best Practices
--------------

To minimize code-duplication, this section will offer some tips on how to organize your breadcrumb handling. As this package was first developed internally for our in-house CMS, it is largely based on how we already handled breadcrumbs. These tips will assume that the project is based on Laravel 4, but it isn't hard to generalize them to any framework you might be using, as most have a similar organization of controllers.

So, we usually have a single `BaseController` which all other controlers extend. Any controller that does something in its constructor is required to call `parent::__construct()` first, so `BaseController::__construct()` is used to setup some basic configuration which is available to all other controllers from that point.

Note that the `BaseController` doesn't have any actions (ie. no routes call `BaseController`) - it's literally there to provide a base for all other controllers.

In `BaseController::__construct()`, we usually (among other things) store a breadcrumbs instance in the class, to make it available to subclasses, and call something like `$this->breadcrumbs->addCrumb('Home', '/')`, which basically adds a first breadcrumb called "Home", which links to the website root - thus, all pages will always have this breadcrumb first (assuming they render breadcrumbs in their views):

```php
public function __construct()
{
    parent::__construct();

    $this->breadcrumbs = new \Creitive\Breadcrumbs\Breadcrumbs;
    $this->breadcrumbs->addCrumb('Home', '/');
}
```

Say we have a `StoreController` that lists various stores available on the website. Of course, this controller extends the `BaseController`, so its constructor might look something like this:

```php
public function __construct()
{
    parent::__construct();

    $this->breadcrumbs->addCrumb('Stores', 'stores');
}
```

Now let's say we have an action `getStores` (which lists available stores), and `getStore` (which lists a single store's homepage). `StoreController::getStores()` wouldn't need to add a breadcrumb of its own, since it is already added in that controller's constructor, under the assumption that all of that controller's actions will have that crumb.

Naturally, `StoreController::getStore()` would do something like `$this->breadcrumbs->addCrumb($store->name, $store->slug)`, so as to add that store's slug as the next breadcrumb.

Rendering the breadcrumbs at this point would yield HTML similar to the following (skipping the `ul`, `li` and divider elements for brevity):

```html
<a href="/">Home</a>
<a href="/stores">Stores</a>
<a href="/stores/foo-store">Foo Store</a>
```

If the system has an admin panel, there is probably no use there for the "Home" breadcrumb added in `BaseController::__construct()`. We usually solve this by having an `Admin\BaseController` (which extends `BaseController`, has no actions, and all administrative controllers extend it), and do something like this in its constructor:

```php
public function __construct()
{
    parent::__construct();

    $this->breadcrumbs->removeAll();
    $this->breadcrumbs->addCrumb('Home', '/admin');
}
```

Now a controller like `Admin\StoreController` (which extends `Admin\BaseController`) can do `$this->breadcrumbs->addCrumb('Stores', 'stores')` in its constructor (after calling `parent::__construct()` of course), to add its own breadcrumb.

For a bit more complex application, we tend not to hardcode routes like that all over the place, so we'll probably do stuff like:

```php
$this->breadcrumbs->addCrumb(Lang::('pages.home'), URL::action('HomeController@getIndex'));
$this->breadcrumbs->addCrumb(Lang::('pages.stores'), URL::action('StoreController@getStores'));
$this->breadcrumbs->addCrumb($store->name, URL::action('StoreController@getStore', array('storeId' => $store->id)));
```

This is a bit out of the scope of this package's documentation, though, so we won't elaborate much more on that, since it extends into a more general explanation of how to organize your application (and assumes access to some of Laravel's infrastructure, which enables us to generate URLs based on the defined application routes).


Recommended CSS
---------------

This plugin uses the `breadcrumbs` CSS class on the containing `ul` element by default, which is what we used in our in-house CSS framework. [Bootstrap](http://getbootstrap.com) uses `breadcrumb` by default, so if you're using Bootstrap's CSS, you should configure it with `$this->breadcrumbs->setCssClasses('breadcrumb')`, for example in `BaseController::__construct()`.

Additionaly, the package will always add the class `active` to the last `li` element (which contains the last breadcrumb added), and a `span` element with a `divider` class will be rendered between all breadcrumbs.

If you're looking for some bare minimum CSS needed to get breadcrumbs working, you can use the code suggested by @SaintPeter in issue #6 of this project's issue tracker:

```css
.breadcrumbs {
    list-style: none;
    overflow: hidden;
}

.breadcrumbs li {
    float: left;
}
```


Further Thoughts and Alternatives
---------------------------------

This package is intended for very basic and simple breadcrumb handling, and it's something we've used for a long time, though initially not with Laravel.

We have considered upgrading it with advanced functionality, like supporting breadcrumb configuration, along with being able to reference parent breadcrumbs or use closures, or being able to define your own views for rendering, but we have decided against all that.

There is already a very powerful breadcrumbs package that does most of that - https://github.com/davejamesmiller/laravel-breadcrumbs - we highly recommend this package for complex projects, and we use it ourselves. If you're looking for alternatives, we've found another package available at https://github.com/noherczeg/breadcrumb but we haven't tried that one.


License
-------

The code is licensed under the MIT license, which is available in the `LICENSE` file.
