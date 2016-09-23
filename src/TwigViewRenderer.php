<?php
/**
 * This file is a part of the bluemvc-twig package.
 *
 * Read more at https://bluemvc.com/
 */
namespace BlueMvc\Twig;

use BlueMvc\Core\Base\AbstractViewRenderer;
use BlueMvc\Core\Interfaces\ApplicationInterface;
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
     * @param ApplicationInterface $application    The application.
     * @param FilePathInterface    $viewsDirectory The views directory.
     * @param FilePathInterface    $viewFile       The view file.
     * @param mixed|null           $model          The model or null if there is no model.
     * @param mixed                $viewData       The view data or null if there is no view data.
     *
     * @return string The rendered view.
     */
    public function renderView(ApplicationInterface $application, FilePathInterface $viewsDirectory, FilePathInterface $viewFile, $model = null, $viewData = null)
    {
        $cachePath = $application->getTempPath()->withFilePath(
            FilePath::parse('bluemvc-twig' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR)
        );

        // Setup and render with Twig.
        $twigLoader = new \Twig_Loader_Filesystem($viewsDirectory->__toString());
        $twigEnvironment = new \Twig_Environment($twigLoader, [
            'cache'       => $cachePath->__toString(),
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
