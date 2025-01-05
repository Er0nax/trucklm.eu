<?php

namespace src\services\twig;

use src\App;

/**
 * @author Tim Zapfe
 * @date 19.11.2024
 */
class Filters
{

    /**
     * Returns a translated string.
     * @param string|null $value
     * @param string $category
     * @param array $variables
     * @return string|null
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    public function t(?string $value, string $category = 'site', array $variables = []): ?string
    {
        return App::t($value, $category, $variables);
    }
}