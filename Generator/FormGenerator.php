<?php

namespace DL\MeatUp\Generator;

use DL\MeatUp\Util\FormImportUtil;
use DL\MeatUp\Util\ReflectionUtil;
use DL\MeatUp\Util\TwigUtil;

final class FormGenerator
{
    public static function generate(ReflectionUtil $reflection, $meatUpDir, $entityBundleNameSpace) {
        $entityClassName = $reflection->getClassShortName();

        $fields = array();
        $imports = array();

        foreach ($reflection->getProperties() as $property) {
            if ($reflection->isId($property)) {
                continue;
            }

            $field = array();

            $field['name'] = $reflection->getName($property);
            $field['label'] = ucfirst($field['name']);
            $field['type'] = $reflection->getTyp($property);
            $field['required'] = $reflection->getRequired($property);

            $import = FormImportUtil::getImport($field['type']);

            if (!in_array($import, $imports)) {
                $imports[] = $import;
            }

            $fields[] = $field;
        }

        sort($imports);

        $twig = TwigUtil::getTwigEnvironment($meatUpDir);

        $formType = $twig->render('formType.php.twig',
            array(
                'namespace' => $entityBundleNameSpace . '\Form\Type',
                'className' => $entityClassName. 'Type',
                'fields' => $fields,
                'imports' => $imports
            )
        );

        return $formType;
    }
}