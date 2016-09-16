<?php
/**
 * This file is a part of the bluemvc-twig package.
 *
 * Read more at https://bluemvc.com/
 */
namespace BlueMvc\Twig;

use BlueMvc\Core\Base\AbstractViewRenderer;
use DataTypes\FilePath;
use DataTypes\Interfaces\FilePathInterface;

/**
 * Class representing a Twig view renderer.
 *
 * @since 1.0.0
 */
class TwigViewRenderer extends AbstractViewRenderer
{
    /**
     * Constructs the Twig view renderer.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        parent::__construct('twig');
    }

    /**
     * Renders the view.
     *
     * @since 1.0.0
     *
     * @param FilePathInterface $viewsDirectory The views directory.
     * @param FilePathInterface $viewFile       The view file.
     * @param mixed|null        $model          The model or null if there is no model.
     * @param mixed             $viewData       The view data or null if there is no view data.
     *
     * @return string The rendered view.
     */
    public function renderView(FilePathInterface $viewsDirectory, FilePathInterface $viewFile, $model = null, $viewData = null)
    {
        // Figure out the cache directory path by temp dir and present views directory.
        $cachePath = FilePath::tryParse(
            sys_get_temp_dir() . DIRECTORY_SEPARATOR .
            'bluemvc-twig' . DIRECTORY_SEPARATOR .
            'templates' . DIRECTORY_SEPARATOR .
            $viewsDirectory->toRelative()
        );

        // Setup and render with Twig.
        $twigLoader = new \Twig_Loader_Filesystem($viewsDirectory->__toString());
        $twigEnvironment = new \Twig_Environment($twigLoader, [
            'cache'       => $cachePath !== null ? $cachePath->__toString() : false,
            'auto_reload' => true,
        ]);
        $twigTemplate = $twigEnvironment->loadTemplate($viewFile->__toString());

        return $twigTemplate->render(
            [
                'Model'    => $model !== null ? $model : [],
                'ViewData' => $viewData !== null ? $viewData : [],
            ]
        );
    }
}
