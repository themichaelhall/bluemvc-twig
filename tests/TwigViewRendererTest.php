<?php

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
            FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR),
            FilePath::parse('basic.twig')
        );

        $this->assertSame('<html><body><h1></h1><p></p></body></html>', $result);
    }

    /**
     * Test the renderView method with model.
     */
    public function testRenderViewWithModel()
    {
        $viewRenderer = new TwigViewRenderer();
        $result = $viewRenderer->renderView(
            FilePath::parse(__DIR__ . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'TestViews' . DIRECTORY_SEPARATOR),
            FilePath::parse('basic.twig'),
            [
                'Header'  => 'The header',
                'Content' => 'The content',
            ]
        );

        $this->assertSame('<html><body><h1>The header</h1><p>The content</p></body></html>', $result);
    }
}
