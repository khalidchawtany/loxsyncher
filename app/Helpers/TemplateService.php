<?php

namespace App\Helpers;

use App\Exceptions\AjaxException;
use App\Models\Check;
use App\Models\CheckStatusViewWithUnloaded;
use App\Models\Product;

class TemplateService
{
    public static function getTemplate(Product $product, $check, TemplateType $templateType, $variant = null)
    {
        if ($check instanceof CheckStatusViewWithUnloaded) {
            $check = Check::find($check->check_id);
        }

        $productTemplateData = self::getProductTemplateData($product, $check);

        if ($productTemplateData == null) {
            throw new AjaxException("Product ($product->kurdish_name) has no editor template for '{$check->checkType->category}' category.");
        }

        // this product is using the old way of using a string to specify the template name
        if (is_string($productTemplateData)) {
            $templateName = $productTemplateData;

            return self::returnTemplateIfExists($templateName, $templateType);
        }

        // this is a new test so we can use the default template version
        if (collect($check->extra)->isEmpty()) {
            $templateName = self::getDefaultTemplateName($productTemplateData, $variant);


            return self::returnTemplateIfExists($templateName, $templateType);
        }

        $templateName = self::getTemplateNameFromCheck($check, $productTemplateData, $variant);

        return self::returnTemplateIfExists($templateName, $templateType);
    }

    private static function getProductTemplateData(Product $product, $check)
    {
        $category = $check->checkType['category'];

        $productTemplateData = $product->extra->get(strtolower($category), null);

        return $productTemplateData;
    }

    private static function getDefaultTemplateName($productTemplateData, $variant = null)
    {
        $defaultVersion = $productTemplateData['default'];

        $selectedVersion = $productTemplateData[$defaultVersion];

        // template name is set when the UI asks for a variation of a template
        // For example: a steel bar could have both iso and astm templates
        if ($variant !== null) {
            $selectedVersionTemplates = $selectedVersion['templates'];
            if (!isset($selectedVersionTemplates[$variant])) {
                throw new AjaxException("Template ($variant) not found.");
            }

            return $selectedVersionTemplates[$variant];
        }

        if (isset($selectedVersion['selector'])) {
            return $selectedVersion['selector'];
        }

        return $selectedVersion['template'];
    }

    private static function getLastTemplate($productTemplateData)
    {
        $lastVersion = collect($productTemplateData)->max('version');

        return $productTemplateData[$lastVersion]['template'];
    }

    private static function returnTemplateIfExists($template, TemplateType $templateType)
    {
        $templatePath = $templateType->getPath();

        if (is_string($template)) {
            $fullTemplatePath = $templatePath . $template;
        } else {
            $fullTemplatePath = $templatePath . $template[strtolower($templateType->getKey())];
        }

        if (view()->exists($fullTemplatePath)) {
            return $fullTemplatePath;
        }

        throw new AjaxException("Template ($fullTemplatePath) not found.");
    }

    private static function getTemplateNameFromCheck($check, $productTemplateData, $variant = null)
    {
        $templateVersionUsedForCheck = collect($check->extra)->get('template_version', null);

        if (empty($templateVersionUsedForCheck)) {
            return $productTemplateData['0']['template'];
        }

        if (isset($productTemplateData[$templateVersionUsedForCheck]['templates'])) {
            if (empty($variant)) {
                $variant = collect($check->extra)->get('template_variant', null);
            }
            return $productTemplateData[$templateVersionUsedForCheck]['templates'][$variant];
        }

        return $productTemplateData[$templateVersionUsedForCheck]['template'];
    }
}
