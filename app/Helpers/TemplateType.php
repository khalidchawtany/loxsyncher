<?php

namespace App\Helpers;

use MyCLabs\Enum\Enum;

class TemplateType extends Enum
{
    private const EDITOR = 'checks.editor_templates.';

    private const REPORT = 'checks.report_templates.';

    private const TEST_PAPER = 'checks.test_paper_templates.';

    public function getPath()
    {
        return $this->getValue();
    }
}
