<?php

namespace Creitive\Breadcrumbs;

use Creitive\Breadcrumbs\Breadcrumbs;
use Exception;
use PHPUnit_Framework_TestCase;
use stdClass;
use Symfony\Component\DomCrawler\Crawler;

class BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
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
        foreach ($crumbs as $key => $validCrumb) {
            $this->assertTrue(Breadcrumbs::isValidCrumb($validCrumb));
        }

        $invalidCrumb = array();

        $this->assertFalse(Breadcrumbs::isValidCrumb($invalidCrumb));
    }

    /**
     * Tests whether `Breadcrumbs::isValidCrumb()` provides proper validation
     * for invalid crumbs.
     *
     * @dataProvider invalidCrumbsProvider
     */
    public function testIsNotValidCrumb($crumbs)
    {
        foreach ($crumbs as $key => $invalidCrumb) {
            $this->assertFalse(Breadcrumbs::isValidCrumb($invalidCrumb));
        }
    }

    /**
     * @dataProvider cssClassesProvider
     */
    public function testCssClassesMethods($classes)
    {
        $b = new Breadcrumbs(array(), $classes);
        $this->assertCount(count($classes), $b->getBreadcrumbsCssClasses());

        $b->removeCssClasses($classes);
        $this->assertCount(0, $b->getBreadcrumbsCssClasses());

        $b->addCssClasses($classes);
        $this->assertCount(count($classes), $b->getBreadcrumbsCssClasses());
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

        $crawler = new Crawler($b->render());

        $dividerText = $crawler->filter('span.divider')->first()->text();

        $this->assertSame('@', $dividerText);
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

        $crawler = new Crawler($b->render());

        /**
         * There should only be one `ol` element.
         */
        $this->assertCount(1, $crawler->filter('ol'));
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

        foreach ($crumbs as $crumb) {
            $b->addCrumb($crumb);
        }

        $this->assertCount(count($crumbs), $b->getBreadcrumbs());
    }

    /**
     * @testdox Is able to enchain `addCrumb` method
     * @depends testAddCrumb
     * @dataProvider crumbsProvider
     */
    public function testIsAbleToEnchainAddCrumbMethod($crumbs)
    {
        $b = new Breadcrumbs;
        $n = count($crumbs);

        switch ($n) {
            case 1:
                $b->addCrumb($crumbs[0]);

                break;

            case 2:
                $b->addCrumb($crumbs[0])
                    ->addCrumb($crumbs[1]);

                break;

            case 3:
                $b->addCrumb($crumbs[0])
                    ->addCrumb($crumbs[1])
                    ->addCrumb($crumbs[2]);

                break;

            case 4:
                $b->addCrumb($crumbs[0])
                    ->addCrumb($crumbs[1])
                    ->addCrumb($crumbs[2])
                    ->addCrumb($crumbs[3]);

                break;

            case 5:
                $b->addCrumb($crumbs[0])
                    ->addCrumb($crumbs[1])
                    ->addCrumb($crumbs[2])
                    ->addCrumb($crumbs[3])
                    ->addCrumb($crumbs[4]);

                break;

            default:
                throw new Exception('Test does not handle more than 5 breadcrumbs.');

                break;
        }

        $this->assertCount($n, $b->getBreadcrumbs());
    }


    /**
     * @testdox `add` can be used as an alias for `addCrumb` method
     * @depends testAddCrumb
     * @dataProvider crumbsProvider
     */
    public function testAddCrumbAlias($crumbs)
    {
        $b = new Breadcrumbs;

        foreach ($crumbs as $crumb) {
            $b->add($crumb);
        }

        $this->assertCount(count($crumbs), $b->getBreadcrumbs());
    }

    /**
     * @testdox Is able to enchain `add` method
     * @depends testIsAbleToEnchainAddCrumbMethod
     * @dataProvider crumbsProvider
     */
    public function testIsAbleToEnchainAddMethod($crumbs)
    {
        $b = new Breadcrumbs;
        $n = count($crumbs);

        switch ($n) {
            case 1:
                $b->add($crumbs[0]);

                break;

            case 2:
                $b->add($crumbs[0])
                    ->add($crumbs[1]);

                break;

            case 3:
                $b->add($crumbs[0])
                    ->add($crumbs[1])
                    ->add($crumbs[2]);

                break;

            case 4:
                $b->add($crumbs[0])
                    ->add($crumbs[1])
                    ->add($crumbs[2])
                    ->add($crumbs[3]);

                break;

            case 5:
                $b->add($crumbs[0])
                    ->add($crumbs[1])
                    ->add($crumbs[2])
                    ->add($crumbs[3])
                    ->add($crumbs[4]);

                break;

            default:
                throw new Exception('Test does not handle more than 5 breadcrumbs.');

                break;
        }

        $this->assertCount($n, $b->getBreadcrumbs());
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

        $this->assertCount(count($crumbs), $b->getBreadcrumbs());
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

        $crawler = new Crawler($b->render());

        /**
         * There should only be one `ul` element.
         */
        $this->assertCount(1, $crawler->filter('ul'));

        /**
         * There should be as many `li` elements as there are breadcrumbs in the
         * original data.
         */
        $this->assertCount(count($crumbs), $crawler->filter('li'));

        /**
         * There should be one `span.divider` less than there are breadcrumbs.
         */
        $this->assertCount(count($crumbs)-1, $crawler->filter('span.divider'));
    }

    /**
     * Tests that the output has the correct CSS classes applied.
     *
     * @dataProvider crumbsWithCssClassesProvider
     */
    public function testOutputCssClasses($crumbs, $classes)
    {
        $b = new Breadcrumbs($crumbs, $classes);

        $crawler = new Crawler($b->render());

        $normalizedExpectedClasses = $classes;
        sort($normalizedExpectedClasses);

        $ulClasses = $crawler->filter('ul')->first()->attr('class');
        $normalizedUlClasses = explode(' ', trim($ulClasses));
        sort($normalizedUlClasses);

        $this->assertSame($normalizedExpectedClasses, $normalizedUlClasses);
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

        $crawler = new Crawler($b->render());

        /**
         * There should be no `span.divider` elements present.
         */
        $this->assertCount(0, $crawler->filter('span.divider'));
    }

    /**
     * Tests whether full URLs are recognized correctly.
     *
     * @dataProvider crumbsProvider
     */
    public function testFullUrls($crumbs)
    {
        $b = new Breadcrumbs($crumbs);

        foreach ($b->getBreadcrumbs() as $key => $crumb) {
            $originalCrumb = $crumbs[$key];

            $hrefIsFullUrl = $crumb['hrefIsFullUrl'];

            if (mb_substr($originalCrumb['href'], 0, 7) === 'http://') {
                $this->assertTrue($hrefIsFullUrl);
            } elseif (mb_substr($originalCrumb['href'], 0, 8) === 'https://') {
                $this->assertTrue($hrefIsFullUrl);
            } elseif (mb_substr($originalCrumb['href'], 0, 1) === '/') {
                $this->assertTrue($hrefIsFullUrl);
            } else {
                $this->assertFalse($hrefIsFullUrl);
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

        $this->assertEquals(count($crumbs), $b->count());
    }

    /**
     * Tests whether `Breadcrumbs::isEmpty()` works correctly.
     */
    public function testIsEmpty()
    {
        $b = new Breadcrumbs();

        $this->assertTrue($b->isEmpty());

        $b->addCrumb('foo', 'bar');

        $this->assertFalse($b->isEmpty());
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

        $this->assertTrue($b->isEmpty());
    }
}
