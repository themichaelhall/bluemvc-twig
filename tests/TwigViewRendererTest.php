<?php

namespace BlueMvc\Twig\Tests;

use BlueMvc\Fakes\FakeApplication;
use BlueMvc\Twig\TwigViewRenderer;
use DataTypes\FilePath;

/**
 * Test TwigViewRenderer class.
 */
class TwigViewRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the renderView method with empty model.
     */
    public function testRenderViewWithEmptyModel()
    {
        $viewRenderer = new TwigViewRenderer();
        $result = $viewRenderer->renderView(
            new FakeApplication(),
            FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR),
            FilePath::parse('basic.twig')
        );

        self::assertSame('<html><head><title></title></head><body><h1></h1><p></p></body></html>', $result);
    }

    /**
     * Test the renderView method with model.
     */
    public function testRenderViewWithModel()
    {
        $viewRenderer = new TwigViewRenderer();
        $result = $viewRenderer->renderView(
            new FakeApplication(),
            FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR),
            FilePath::parse('basic.twig'),
            [
                'Header'  => 'The header',
                'Content' => 'The content',
            ]
        );

        self::assertSame('<html><head><title></title></head><body><h1>The header</h1><p>The content</p></body></html>', $result);
    }

    /**
     * Test the renderView method with model and view data.
     */
    public function testRenderViewWithModelAndViewData()
    {
        $viewRenderer = new TwigViewRenderer();
        $result = $viewRenderer->renderView(
            new FakeApplication(),
            FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR),
            FilePath::parse('basic.twig'),
            [
                'Header'  => 'The header',
                'Content' => 'The content',
            ],
            [
                'Title' => 'The title',
            ]
        );

        self::assertSame('<html><head><title>The title</title></head><body><h1>The header</h1><p>The content</p></body></html>', $result);
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
     * Test render a view using a custom Twig extension.
     */
    public function testRenderViewWithCustomTwigExtension()
    {
        $viewRenderer = new TwigViewRenderer();

        $twigEnvironment = $viewRenderer->getTwigEnvironment();
        $twigEnvironment->addFilter(new \Twig_SimpleFilter('FooBar', function ($s) {
            return strtoupper($s);
        }));

        $result = $viewRenderer->renderView(
            new FakeApplication(),
            FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR),
            FilePath::parse('extension.twig'),
            'Baz'
        );

        self::assertSame('<html><head><title></title></head><body><p>BAZ</p></body></html>', $result);
    }

    /**
     * Test get the Twig cache directory.
     */
    public function testGetTwigCacheDirectory()
    {
        $application = new FakeApplication();
        $viewRenderer = new TwigViewRenderer();
        $twigEnvironment = $viewRenderer->getTwigEnvironment();

        $viewRenderer->renderView(
            $application,
            FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR),
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
        $viewRenderer = new TwigViewRenderer();
        $twigEnvironment = $viewRenderer->getTwigEnvironment();
        $twigEnvironment->setCache(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'twig-test' . DIRECTORY_SEPARATOR);

        $viewRenderer->renderView(
            $application,
            FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR),
            FilePath::parse('basic.twig')
        );

        self::assertSame(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'twig-test' . DIRECTORY_SEPARATOR, $twigEnvironment->getCache());
    }
}
