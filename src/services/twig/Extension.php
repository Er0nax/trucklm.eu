<?php

namespace src\services\twig;

use ReflectionClass;
use ReflectionMethod;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Tim Zapfe
 * @date 19.11.2024
 */
class Extension extends AbstractExtension
{
    /**
     * @var Functions
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    private Functions $functions;

    /**
     * @var Filters
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    private Filters $filters;

    /**
     * Konstruktor, um die `Variables` Klasse zu initialisieren
     */
    public function __construct()
    {
        $this->functions = new Functions();
        $this->filters = new Filters();
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        $functions = [];

        // ReflectionClass to get all functions
        $reflection = new ReflectionClass($this->functions);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        // add functions
        foreach ($methods as $method) {
            $methodName = $method->getName();
            $functions[] = new TwigFunction($methodName, [$this->functions, $methodName]);
        }

        return $functions;
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        $functions = [];

        // ReflectionClass to get all functions
        $reflection = new ReflectionClass($this->filters);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        // add filters
        foreach ($methods as $method) {
            $methodName = $method->getName();
            $functions[] = new TwigFilter($methodName, [$this->filters, $methodName]);
        }

        return $functions;
    }
}