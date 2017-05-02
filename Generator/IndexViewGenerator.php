<?php

namespace DL\MeatUp\Generator;

use DL\MeatUp\Util\ReflectionUtil;
use DL\MeatUp\Util\TwigUtil;

final class IndexViewGenerator
{
    public static function generate(ReflectionUtil $reflection, $meatUpDir, $entityClassName)
    {
        $indexProperties = array();
        $indexPropertyLabels = array();
        $indexPropertyFilters = array();
        $indexPropertyFilterArguments = array();

        foreach ($reflection->getProperties() as $property)
        {
            if ($reflection->hasOnIndexPage($property))
            {
                $propertyName = $property->getName();
                $indexProperties[] = $propertyName;

                $indexPropertyLabel = $reflection->getOnIndexPageLabel($property);
                $indexPropertyLabels[] = !empty($indexPropertyLabel) ? $indexPropertyLabel : ucfirst($propertyName);

                $indexPropertyFilter = $reflection->getOnIndexPageFilter($property);
                $indexPropertyFilters[]  = !empty($indexPropertyFilter) ? $indexPropertyFilter : '';
            }
        }

        $twig = TwigUtil::getTwigEnvironment($meatUpDir);

        $indexView = $twig->render('views/index.html.twig.twig',
            array(
                'name' => $entityClassName,
                'indexPropertyLabels' => $indexPropertyLabels,
                'indexPropertyNames' => $indexProperties,
                'indexPropertyFilters' => $indexPropertyFilters
            )
        );

        return $indexView;
    }
}