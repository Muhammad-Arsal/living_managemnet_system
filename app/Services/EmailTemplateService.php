<?php

namespace App\Services;

use App\Models\EmailTemplate;

class EmailTemplateService
{
    /**
     * @param  array<string, string>  $replacements
     * @return array{subject: string, content: string}|null
     */
    public function render(string $emailType, array $replacements = []): ?array
    {
        $template = EmailTemplate::getByType($emailType);

        if (! $template) {
            return null;
        }

        return [
            'subject' => $template->renderSubject($replacements),
            'content' => $template->renderHtmlContent($replacements),
        ];
    }
}
