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

        $this->myTwigLoader = new \Twig_Loader_Filesystem();
        $this->myTwigEnvironment = new \Twig_Environment($this->myTwigLoader, [
            'auto_reload' => true,
        ]);
    }

    /**
     * Returns the Twig environment.
     *
     * @since 1.0.0
     *
     * @return \Twig_Environment The Twig environment.
     */
    public function getTwigEnvironment()
    {
        return $this->myTwigEnvironment;
    }

    /**
     * Returns the Twig loader.
     *
     * @since 1.0.0
     *
     * @return \Twig_Loader_Filesystem The Twig loader.
     */
    public function getTwigLoader()
    {
        return $this->myTwigLoader;
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
        // Set views directory path.
        $this->myTwigLoader->setPaths($viewsDirectory->__toString());

        // Set cache path if not set yet.
        if ($this->myTwigEnvironment->getCache() === false) {
            $cachePath = $application->getTempPath()->withFilePath(
                FilePath::parse('bluemvc-twig' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR)
            );

            $this->myTwigEnvironment->setCache($cachePath->__toString());
        }

        // Create and render template.
        $twigTemplate = $this->myTwigEnvironment->load($viewFile->__toString());

        return $twigTemplate->render(
            [
                'Model'    => $model !== null ? $model : [],
                'ViewData' => $viewData !== null ? $viewData : [],
            ]
        );
    }

    /**
     * @var \Twig_Loader_Filesystem My Twig loader.
     */
    private $myTwigLoader;

    /**
     * @var \Twig_Environment My Twig environment.
     */
    private $myTwigEnvironment;
}
