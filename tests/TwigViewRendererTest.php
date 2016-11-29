<?php

use BlueMvc\Fakes\FakeApplication;
use BlueMvc\Twig\TwigViewRenderer;
use DataTypes\FilePath;

/**
 * Test TwigViewRenderer class.
 */
class TwigViewRendererTest extends PHPUnit_Framework_TestCase
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

        $this->assertSame('<html><head><title></title></head><body><h1></h1><p></p></body></html>', $result);
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

        $this->assertSame('<html><head><title></title></head><body><h1>The header</h1><p>The content</p></body></html>', $result);
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

        $this->assertSame('<html><head><title>The title</title></head><body><h1>The header</h1><p>The content</p></body></html>', $result);
    }

    /**
     * Test getTwigLoader method.
     */
    public function testGetTwigLoader()
    {
        $viewRenderer = new TwigViewRenderer();

        $this->assertInstanceOf(\Twig_Loader_Filesystem::class, $viewRenderer->getTwigLoader());
    }

    /**
     * Test getTwigEnvironment method.
     */
    public function testGetTwigEnvironment()
    {
        $viewRenderer = new TwigViewRenderer();

        $this->assertInstanceOf(\Twig_Environment::class, $viewRenderer->getTwigEnvironment());
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

        $this->assertSame('<html><head><title></title></head><body><p>BAZ</p></body></html>', $result);
    }
}
