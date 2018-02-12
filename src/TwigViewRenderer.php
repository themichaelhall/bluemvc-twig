<?php
/**
 * This file is a part of the bluemvc-twig package.
 *
 * Read more at https://bluemvc.com/
 */

namespace BlueMvc\Twig;

use BlueMvc\Core\Base\AbstractViewRenderer;
use BlueMvc\Core\Interfaces\ApplicationInterface;
use BlueMvc\Core\Interfaces\Collections\ViewItemCollectionInterface;
use BlueMvc\Core\Interfaces\RequestInterface;
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
     *
     * @param string $viewFileExtension The view file extension.
     */
    public function __construct($viewFileExtension = 'twig')
    {
        parent::__construct($viewFileExtension);

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
     * @since    1.0.0
     *
     * @param ApplicationInterface        $application The application.
     * @param RequestInterface            $request     The request.
     * @param FilePathInterface           $viewFile    The view file.
     * @param mixed|null                  $model       The model or null if there is no model.
     * @param ViewItemCollectionInterface $viewItems   The view items or null if there is no view items.
     *
     * @return string The rendered view.
     */
    public function renderView(ApplicationInterface $application, RequestInterface $request, FilePathInterface $viewFile, $model = null, ViewItemCollectionInterface $viewItems = null)
    {
        // Set views directory path.
        $this->myTwigLoader->setPaths($application->getViewPath()->__toString());

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
                'Model'       => $model !== null ? $model : [],
                'ViewItems'   => $viewItems !== null ? iterator_to_array($viewItems) : [],
                'Request'     => $request,
                'Application' => $application,
            ]
        );
    }

    /**
     * Sets whether strict variables should be enabled.
     *
     * @since 1.1.0
     *
     * @param bool $isEnabled True if strict variables should be enabled, false otherwise.
     *
     * @return self The Twig view renderer.
     */
    public function setStrictVariables($isEnabled = true)
    {
        if (!is_bool($isEnabled)) {
            throw new \InvalidArgumentException('$isEnabled parameter is not a boolean.');
        }

        if ($isEnabled) {
            $this->myTwigEnvironment->enableStrictVariables();
        } else {
            $this->myTwigEnvironment->disableStrictVariables();
        }

        return $this;
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
