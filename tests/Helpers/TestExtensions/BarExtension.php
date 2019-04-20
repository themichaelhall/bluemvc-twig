<?php

declare(strict_types=1);

namespace BlueMvc\Twig\Tests\Helpers\TestExtensions;

use Twig_Extension;
use Twig_SimpleFilter;

/**
 * Test extension that implements the filter "Bar".
 */
class BarExtension extends Twig_Extension
{
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @return Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        return [
            new Twig_SimpleFilter('Bar', [$this, 'barFilter']),
        ];
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function barFilter(string $text): string
    {
        return strtolower($text);
    }
}
