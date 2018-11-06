<?php

declare(strict_types=1);

namespace BlueMvc\Twig\Tests;

use BlueMvc\Core\Collections\ViewItemCollection;
use BlueMvc\Fakes\FakeApplication;
use BlueMvc\Fakes\FakeRequest;
use BlueMvc\Twig\Tests\Helpers\TestExtensions\BarExtension;
use BlueMvc\Twig\TwigViewRenderer;
use DataTypes\FilePath;
use PHPUnit\Framework\TestCase;

/**
 * Test TwigViewRenderer class.
 */
class TwigViewRendererTest extends TestCase
{
    /**
     * Test the renderView method with empty model.
     */
    public function testRenderViewWithEmptyModel()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();
        $result = $viewRenderer->renderView(
            $application,
            new FakeRequest('/foo'),
            FilePath::parse('basic.twig')
        );

        self::assertSame('<html><head><title></title></head><body><h1></h1><p></p><p>http://localhost/foo</p><p>' . $application->getViewPath() . '</p></body></html>', $result);
    }

    /**
     * Test the renderView method with model.
     */
    public function testRenderViewWithModel()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();
        $result = $viewRenderer->renderView(
            $application,
            new FakeRequest('/bar'),
            FilePath::parse('basic.twig'),
            [
                'Header'  => 'The header',
                'Content' => 'The content',
            ]
        );

        self::assertSame('<html><head><title></title></head><body><h1>The header</h1><p>The content</p><p>http://localhost/bar</p><p>' . $application->getViewPath() . '</p></body></html>', $result);
    }

    /**
     * Test the renderView method with model and view data.
     */
    public function testRenderViewWithModelAndViewData()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();
        $viewItems = new ViewItemCollection();
        $viewItems->set('Title', 'The title');
        $result = $viewRenderer->renderView(
            $application,
            new FakeRequest(),
            FilePath::parse('basic.twig'),
            [
                'Header'  => 'The header',
                'Content' => 'The content',
            ],
            $viewItems
        );

        self::assertSame('<html><head><title>The title</title></head><body><h1>The header</h1><p>The content</p><p>http://localhost/</p><p>' . $application->getViewPath() . '</p></body></html>', $result);
    }

    /**
     * Test getTwigLoader method.
     */
    public function testGetTwigLoader()
    {
        $viewRenderer = new TwigViewRenderer();

        self::assertInstanceOf(\Twig_Loader_Filesystem::class, $viewRenderer->getTwigLoader());
    }

    /**
     * Test getTwigEnvironment method.
     */
    public function testGetTwigEnvironment()
    {
        $viewRenderer = new TwigViewRenderer();

        self::assertInstanceOf(\Twig_Environment::class, $viewRenderer->getTwigEnvironment());
    }

    /**
     * Test render a view using a custom Twig filter.
     */
    public function testRenderViewWithCustomTwigFilter()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();

        $twigEnvironment = $viewRenderer->getTwigEnvironment();
        $twigEnvironment->addFilter(new \Twig_SimpleFilter('Foo', function ($s) {
            return strtoupper($s);
        }));

        $result = $viewRenderer->renderView(
            $application,
            new FakeRequest(),
            FilePath::parse('with-filter.twig'),
            'Baz'
        );

        self::assertSame('<html><head><title></title></head><body><p>BAZ</p></body></html>', $result);
    }

    /**
     * Test render a view using a custom Twig extension.
     */
    public function testRenderViewWithCustomTwigExtension()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();
        $viewRenderer->addExtension(new BarExtension());

        $result = $viewRenderer->renderView(
            $application,
            new FakeRequest(),
            FilePath::parse('with-extension.twig'),
            'Baz'
        );

        self::assertSame('<html><head><title></title></head><body><p>baz</p></body></html>', $result);
    }

    /**
     * Test get the Twig cache directory.
     */
    public function testGetTwigCacheDirectory()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();
        $twigEnvironment = $viewRenderer->getTwigEnvironment();

        $viewRenderer->renderView(
            $application,
            new FakeRequest(),
            FilePath::parse('basic.twig')
        );

        self::assertSame($application->getTempPath()->withFilePath(FilePath::parse('bluemvc-twig' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR))->__toString(), $twigEnvironment->getCache());
    }

    /**
     * Test that the an existing Twig cache directory does not change after View rendering.
     */
    public function testExistingTwigCacheDirectoryDoesNotChangeAfterViewRendering()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();
        $twigEnvironment = $viewRenderer->getTwigEnvironment();
        $twigEnvironment->setCache(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'twig-test' . DIRECTORY_SEPARATOR);

        $viewRenderer->renderView(
            $application,
            new FakeRequest(),
            FilePath::parse('basic.twig')
        );

        self::assertSame(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'twig-test' . DIRECTORY_SEPARATOR, $twigEnvironment->getCache());
    }

    /**
     * Test get default view file extension.
     */
    public function testGetDefaultViewFileExtension()
    {
        $viewRenderer = new TwigViewRenderer();

        self::assertSame('twig', $viewRenderer->getViewFileExtension());
    }

    /**
     * Test set view file extension.
     */
    public function testSetViewFileExtension()
    {
        $viewRenderer = new TwigViewRenderer('html.tpl');

        self::assertSame('html.tpl', $viewRenderer->getViewFileExtension());
    }

    /**
     * Test enable strict variables.
     */
    public function testEnableStrictVariables()
    {
        $viewRenderer = (new TwigViewRenderer())->setStrictVariables(true);

        self::assertTrue($viewRenderer->getTwigEnvironment()->isStrictVariables());
    }

    /**
     * Test disable strict variables.
     */
    public function testDisableStrictVariables()
    {
        $viewRenderer = (new TwigViewRenderer())->setStrictVariables(false);

        self::assertFalse($viewRenderer->getTwigEnvironment()->isStrictVariables());
    }

    /**
     * Test that paths are set by default.
     */
    public function testPathsAreSetByDefault()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();
        $viewRenderer->renderView(
            $application,
            new FakeRequest(),
            FilePath::parse('basic.twig')
        );

        self::assertSame([__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews'], $viewRenderer->getTwigLoader()->getPaths());
    }

    /**
     * Test that paths are not changed if set.
     */
    public function testPathsAreNotChangedIfSet()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();
        $viewRenderer->getTwigLoader()->setPaths(
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViewsAlternate',
                __DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews',
            ]
        );

        $viewRenderer->renderView(
            $application,
            new FakeRequest(),
            FilePath::parse('basic.twig')
        );

        self::assertSame([__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViewsAlternate', __DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews'], $viewRenderer->getTwigLoader()->getPaths());
    }

    /**
     * Test render a view with an included file from base directory.
     */
    public function testRenderViewWithIncludedFileFromBaseDirectory()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();

        $result = $viewRenderer->renderView(
            $application,
            new FakeRequest(),
            FilePath::parse('with-include.twig')
        );

        self::assertSame('<html><head><title></title></head><body><p>Included from base directory</p></body></html>', $result);
    }

    /**
     * Test render a view with an included file from alternate directory.
     */
    public function testRenderViewWithIncludedFileFromAlternateDirectory()
    {
        $application = new FakeApplication();
        $application->setViewPath(FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR));
        $viewRenderer = new TwigViewRenderer();
        $viewRenderer->getTwigLoader()->setPaths(
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViewsAlternate' . DIRECTORY_SEPARATOR,
                __DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR,
            ]
        );

        $result = $viewRenderer->renderView(
            $application,
            new FakeRequest(),
            FilePath::parse('with-include.twig')
        );

        self::assertSame('<html><head><title></title></head><body><p>Included from alternate directory</p></body></html>', $result);
    }

    /**
     * Test enable debug mode.
     */
    public function testEnableDebugMode()
    {
        $viewRenderer = (new TwigViewRenderer())->setDebug(true);

        self::assertTrue($viewRenderer->getTwigEnvironment()->isDebug());
    }

    /**
     * Test disable debug mode.
     */
    public function testDisableDebugMode()
    {
        $viewRenderer = (new TwigViewRenderer())->setDebug(false);

        self::assertFalse($viewRenderer->getTwigEnvironment()->isDebug());
    }
}
