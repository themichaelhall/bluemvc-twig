<?php

declare(strict_types=1);

namespace BlueMvc\Twig\Tests\Helpers\TestExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Test extension that implements the filter "Bar".
 */
class BarExtension extends AbstractExtension
{
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('Bar', [$this, 'barFilter']),
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
