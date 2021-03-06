<?php
/**
 * This file is a part of the bluemvc-twig package.
 *
 * Read more at https://bluemvc.com/
 */
declare(strict_types=1);

namespace BlueMvc\Twig;

use BlueMvc\Core\Base\AbstractViewRenderer;
use BlueMvc\Core\Interfaces\ApplicationInterface;
use BlueMvc\Core\Interfaces\Collections\ViewItemCollectionInterface;
use BlueMvc\Core\Interfaces\RequestInterface;
use DataTypes\FilePath;
use DataTypes\Interfaces\FilePathInterface;
use Twig_Environment;
use Twig_ExtensionInterface;
use Twig_Loader_Filesystem;

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
    public function __construct(string $viewFileExtension = 'twig')
    {
        parent::__construct($viewFileExtension);

        $this->twigLoader = new Twig_Loader_Filesystem();
        $this->twigEnvironment = new Twig_Environment($this->twigLoader, [
            'auto_reload' => true,
        ]);
    }

    /**
     * Adds a Twig extension.
     *
     * @since 2.1.0
     *
     * @param Twig_ExtensionInterface $extension The Twig extension.
     *
     * @return self The Twig view renderer.
     */
    public function addExtension(Twig_ExtensionInterface $extension): self
    {
        $this->twigEnvironment->addExtension($extension);

        return $this;
    }

    /**
     * Returns the Twig environment.
     *
     * @since 1.0.0
     *
     * @return Twig_Environment The Twig environment.
     */
    public function getTwigEnvironment(): Twig_Environment
    {
        return $this->twigEnvironment;
    }

    /**
     * Returns the Twig loader.
     *
     * @since 1.0.0
     *
     * @return Twig_Loader_Filesystem The Twig loader.
     */
    public function getTwigLoader(): Twig_Loader_Filesystem
    {
        return $this->twigLoader;
    }

    /**
     * Renders the view.
     *
     * @since 1.0.0
     *
     * @param ApplicationInterface             $application The application.
     * @param RequestInterface                 $request     The request.
     * @param FilePathInterface                $viewFile    The view file.
     * @param mixed|null                       $model       The model or null if there is no model.
     * @param ViewItemCollectionInterface|null $viewItems   The view items or null if there is no view items.
     *
     * @return string The rendered view.
     */
    public function renderView(ApplicationInterface $application, RequestInterface $request, FilePathInterface $viewFile, $model = null, ?ViewItemCollectionInterface $viewItems = null): string
    {
        // Set views directory path if not set.
        if ($this->twigLoader->getPaths() === []) {
            $this->twigLoader->setPaths($application->getViewPath()->__toString());
        }

        // Set cache path if not set.
        if ($this->twigEnvironment->getCache() === false) {
            $cachePath = $application->getTempPath()->withFilePath(
                FilePath::parse('bluemvc-twig' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR)
            );

            $this->twigEnvironment->setCache($cachePath->__toString());
        }

        // Create and render template.
        $twigTemplate = $this->twigEnvironment->load($viewFile->__toString());

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
    public function setStrictVariables(bool $isEnabled = true): self
    {
        if ($isEnabled) {
            $this->twigEnvironment->enableStrictVariables();
        } else {
            $this->twigEnvironment->disableStrictVariables();
        }

        return $this;
    }

    /**
     * Sets whether debug mode should be enabled.
     *
     * @since 2.1.0
     *
     * @param bool $isDebug True if debug mode should be enabled, false otherwise.
     *
     * @return self The Twig view renderer.
     */
    public function setDebug(bool $isDebug = true): self
    {
        if ($isDebug) {
            $this->twigEnvironment->enableDebug();
        } else {
            $this->twigEnvironment->disableDebug();
        }

        return $this;
    }

    /**
     * @var Twig_Loader_Filesystem My Twig loader.
     */
    private $twigLoader;

    /**
     * @var Twig_Environment My Twig environment.
     */
    private $twigEnvironment;
}
