<?php
use Creitive\Breadcrumbs\Breadcrumbs;
use Way\Tests\Assert;
use Way\Tests\Should;

class BreadcrumbsTest extends \PHPUnit_Framework_TestCase {

	public function crumbProvider()
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
		);
	}

	public function classProvider()
	{
		return array(
			array('bcrumb'),
			array('bread'),
		);
	}

	public function fullCrumbProvider()
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
				array(
					'bcrumb',
				),
			),
		);
	}

	/**
	 * @dataProvider crumbProvider
	 */
	public function testAddBreadcrumb($crumbs)
	{
		$b = new Breadcrumbs;

		foreach ($crumbs as $crumb)
		{
			$b->addCrumb($crumb);
		}

		Assert::count(count($crumbs), $b->getBreadcrumbs());
	}

	/**
	 * @dataProvider crumbProvider
	 */
	public function testSetBreadcrumb($crumbs)
	{
		$b = new Breadcrumbs;

		$b->setBreadcrumbs($crumbs);

		Assert::count(count($crumbs), $b->getBreadcrumbs());
	}

	/**
	 * @dataProvider crumbProvider
	 */
	public function testValidCrumb($crumbs)
	{
		foreach($crumbs as $key => $value)
		{
			Assert::true(Breadcrumbs::isValidCrumb($value));
		}

		$falseCrumb = array();
		Assert::false(Breadcrumbs::isValidCrumb($falseCrumb));
	}

	/**
	 * @dataProvider classProvider
	 */
	public function testCssClasses($classes)
	{
		$b = new Breadcrumbs(array(), $classes);
		Assert::count(count($classes), $b->getBreadcrumbsCssClasses());
		$b->removeCssClasses($classes);
		Assert::count(0, $b->getBreadcrumbsCssClasses());
		$b->addCssClasses($classes);
		Assert::count(count($classes), $b->getBreadcrumbsCssClasses());
	}

	public function testDivider()
	{
		$b = new Breadcrumbs();
		$b->setDivider("@");
		Assert::same("@", $b->getDivider());
	}

	/**
	 * @dataProvider fullCrumbProvider
	 */
	public function testOutput($crumbs, $classes)
	{
		$b = new Breadcrumbs($crumbs, $classes);
		$crawler = new Symfony\Component\DomCrawler\Crawler($b->render());
		Assert::count(1, $crawler->filter('ul.'.$classes[0]));
		Assert::count(count($crumbs), $crawler->filter('li'));
		Assert::count(count($crumbs)-1, $crawler->filter('span.divider'));
	}

	/**
	 * @dataProvider fullCrumbProvider
	 */
	public function testOutputWithoutDividers($crumbs, $classes)
	{
		$b = new Breadcrumbs($crumbs, $classes);
		$b->setDivider(null);

		$crawler = new Symfony\Component\DomCrawler\Crawler($b->render());
		Assert::count(1, $crawler->filter('ul.'.$classes[0]));
		Assert::count(count($crumbs), $crawler->filter('li'));
		Assert::count(0, $crawler->filter('span.divider'));
	}
}
