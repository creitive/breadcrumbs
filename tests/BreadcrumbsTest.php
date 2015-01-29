<?php
use Creitive\Breadcrumbs\Breadcrumbs;
use Way\Tests\Assert;
use Way\Tests\Should;

class BreadcrumbsTest extends \PHPUnit_Framework_TestCase {

	public function crumbsProvider()
	{
		return array(
			array(
				array(
					array(
						'name' => 'Home',
						'href' => '/',
					),
					array(
						'name' => 'Products',
						'href' => '/products',
					),
				),
			),
			array(
				array(
					array(
						'name' => 'Home',
						'href' => '/',
					),
					array(
						'name' => 'About',
						'href' => 'about',
					),
					array(
						'name' => 'Organization chart',
						'href' => 'organization-chart',
					),
				),
			),
			array(
				array(
					array(
						'name' => 'Admin home',
						'href' => '/admin',
					),
					array(
						'name' => 'Stores',
						'href' => 'stores',
					),
					array(
						'name' => 'Store Foo',
						'href' => 'http://website.com/admin/stores/store-foo',
					),
					array(
						'name' => 'Secure product creation',
						'href' => 'https://website.com/admin/stores/store-foo/add-product',
					),
				),
			),
		);
	}

	public function invalidCrumbsProvider()
	{
		return array(
			array(
				array(
					array(),
					'test',
					array(
						'name' => 123,
						'href' => new stdClass,
					),
				),
			),
		);
	}

	public function crumbsWithCssClassesProvider()
	{
		return array(
			array(
				array(
					array(
						'name' => 'Admin home',
						'href' => '/admin',
					),
					array(
						'name' => 'Stores',
						'href' => 'stores',
					),
					array(
						'name' => 'Store Foo',
						'href' => 'http://website.com/admin/stores/store-foo',
					),
				),
				array(
					'breadcrumbs-class',
				),
			),
			array(
				array(
					array(
						'name' => 'Admin home',
						'href' => '/admin',
					),
					array(
						'name' => 'Stores',
						'href' => 'stores',
					),
					array(
						'name' => 'Store Foo',
						'href' => 'http://website.com/admin/stores/store-foo',
					),
				),
				array(
					'breadcrumbs-class',
					'additional-breadcrumbs-class',
				),
			),
		);
	}

	public function cssClassesProvider()
	{
		return array(
			array('breadcrumbs-class'),
			array('crumbs'),
		);
	}

	/**
	 * Tests whether `Breadcrumbs::isValidCrumb()` provides proper validation
	 * for valid crumbs.
	 *
	 * @dataProvider crumbsProvider
	 */
	public function testIsValidCrumb($crumbs)
	{
		foreach ($crumbs as $key => $validCrumb)
		{
			Assert::true(Breadcrumbs::isValidCrumb($validCrumb));
		}

		$invalidCrumb = array();

		Assert::false(Breadcrumbs::isValidCrumb($invalidCrumb));
	}

	/**
	 * Tests whether `Breadcrumbs::isValidCrumb()` provides proper validation
	 * for invalid crumbs.
	 *
	 * @dataProvider invalidCrumbsProvider
	 */
	public function testIsNotValidCrumb($crumbs)
	{
		foreach ($crumbs as $key => $invalidCrumb)
		{
			Assert::false(Breadcrumbs::isValidCrumb($invalidCrumb));
		}
	}

	/**
	 * @dataProvider cssClassesProvider
	 */
	public function testCssClassesMethods($classes)
	{
		$b = new Breadcrumbs(array(), $classes);
		Assert::count(count($classes), $b->getBreadcrumbsCssClasses());

		$b->removeCssClasses($classes);
		Assert::count(0, $b->getBreadcrumbsCssClasses());

		$b->addCssClasses($classes);
		Assert::count(count($classes), $b->getBreadcrumbsCssClasses());
	}

	/**
	 * Tests whether `Breadcrumbs::setDivider()` works.
	 */
	public function testSetDivider()
	{
		$b = new Breadcrumbs();

		$b->setDivider('@');

		$b->addCrumb('foo', 'bar');
		$b->addCrumb('foo', 'bar');

		$crawler = new Symfony\Component\DomCrawler\Crawler($b->render());

		$dividerText = $crawler->filter('span.divider')->first()->text();

		Assert::same('@', $dividerText);
	}

	/**
	 * Tests whether `setListElement()` works correctly.
	 *
	 * @dataProvider crumbsProvider
	 */
	public function testSetListElement($crumbs)
	{
	    $b = new Breadcrumbs($crumbs);
	    $b->setListElement('ol');

	    $crawler = new Symfony\Component\DomCrawler\Crawler($b->render());

	    /**
	     * There should only be one `ol` element.
	     */
	    Assert::count(1, $crawler->filter('ol'));
	}

	/**
	 * Tests whether adding breadcrumbs one by one works, using
	 * `Breadcrumbs::addCrumb()`.
	 *
	 * @dataProvider crumbsProvider
	 */
	public function testAddCrumb($crumbs)
	{
		$b = new Breadcrumbs;

		foreach ($crumbs as $crumb)
		{
			$b->addCrumb($crumb);
		}

		Assert::count(count($crumbs), $b->getBreadcrumbs());
	}

	/**
	 * Tests whether setting an array of breadcrumbs at once works, using
	 * `Breadcrumbs::setBreadcrumbs()`.
	 *
	 * @dataProvider crumbsProvider
	 */
	public function testSetBreadcrumbs($crumbs)
	{
		$b = new Breadcrumbs;

		$b->setBreadcrumbs($crumbs);

		Assert::count(count($crumbs), $b->getBreadcrumbs());
	}

	/**
	 * Tests whether `Breadcrumbs::setBreadcrumbs()` throws the correct exception
	 * when called with invalid arguments.
	 *
	 * @dataProvider invalidCrumbsProvider
	 * @expectedException InvalidArgumentException
	 */
	public function testSetBreadcrumbsException($crumbs)
	{
		$b = new Breadcrumbs;

		$b->setBreadcrumbs($crumbs);
	}

	/**
	 * Tests whether the output is correctly rendered.
	 *
	 * Doesn't check the actual items, just that the expected number of items is
	 * present.
	 *
	 * @dataProvider crumbsWithCssClassesProvider
	 */
	public function testOutput($crumbs, $classes)
	{
		$b = new Breadcrumbs($crumbs, $classes);

		$crawler = new Symfony\Component\DomCrawler\Crawler($b->render());

		/**
		 * There should only be one `ul` element.
		 */
		Assert::count(1, $crawler->filter('ul'));

		/**
		 * There should be as many `li` elements as there are breadcrumbs in the
		 * original data.
		 */
		Assert::count(count($crumbs), $crawler->filter('li'));

		/**
		 * There should be one `span.divider` less than there are breadcrumbs.
		 */
		Assert::count(count($crumbs)-1, $crawler->filter('span.divider'));
	}

	/**
	 * Tests that the output has the correct CSS classes applied.
	 *
	 * @dataProvider crumbsWithCssClassesProvider
	 */
	public function testOutputCssClasses($crumbs, $classes)
	{
		$b = new Breadcrumbs($crumbs, $classes);

		$crawler = new Symfony\Component\DomCrawler\Crawler($b->render());

		$normalizedExpectedClasses = $classes;
		sort($normalizedExpectedClasses);

		$ulClasses = $crawler->filter('ul')->first()->attr('class');
		$normalizedUlClasses = explode(' ', trim($ulClasses));
		sort($normalizedUlClasses);

		Assert::same($normalizedExpectedClasses, $normalizedUlClasses);
	}

	/**
	 * Tests that no dividers are rendered if the divider is set to `null`.
	 *
	 * @dataProvider crumbsWithCssClassesProvider
	 */
	public function testOutputWithoutDividers($crumbs, $classes)
	{
		$b = new Breadcrumbs($crumbs, $classes);

		$b->setDivider(null);

		$crawler = new Symfony\Component\DomCrawler\Crawler($b->render());

		/**
		 * There should be no `span.divider` elements present.
		 */
		Assert::count(0, $crawler->filter('span.divider'));
	}

	/**
	 * Tests whether full URLs are recognized correctly.
	 *
	 * @dataProvider crumbsProvider
	 */
	public function testFullUrls($crumbs)
	{
		$b = new Breadcrumbs($crumbs);

		foreach ($b->getBreadcrumbs() as $key => $crumb)
		{
			$originalCrumb = $crumbs[$key];

			$hrefIsFullUrl = $crumb['hrefIsFullUrl'];

			if (mb_substr($originalCrumb['href'], 0, 7) === 'http://')
			{
				Assert::true($hrefIsFullUrl);
			}
			else if (mb_substr($originalCrumb['href'], 0, 8) === 'https://')
			{
				Assert::true($hrefIsFullUrl);
			}
			else if (mb_substr($originalCrumb['href'], 0, 1) === '/')
			{
				Assert::true($hrefIsFullUrl);
			}
			else
			{
				Assert::false($hrefIsFullUrl);
			}
		}
	}

	/**
	 * Tests whether `Breadcrumbs::count()` works correctly.
	 *
	 * @dataProvider crumbsProvider
	 */
	public function testCountBreadcrumbs($crumbs)
	{
		$b = new Breadcrumbs($crumbs);

		Assert::equals(count($crumbs), $b->count());
	}

	/**
	 * Tests whether `Breadcrumbs::isEmpty()` works correctly.
	 */
	public function testIsEmpty()
	{
		$b = new Breadcrumbs();

		Assert::true($b->isEmpty());

		$b->addCrumb('foo', 'bar');

		Assert::false($b->isEmpty());
	}

	/**
	 * Tests whether `Breadcrumbs::removeAll()` works correctly.
	 *
	 * @dataProvider crumbsProvider
	 */
	public function testRemoveAll($crumbs)
	{
		$b = new Breadcrumbs($crumbs);

		$b->removeAll();

		Assert::true($b->isEmpty());
	}
}
