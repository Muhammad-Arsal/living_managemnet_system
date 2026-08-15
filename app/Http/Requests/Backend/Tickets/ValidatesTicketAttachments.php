<?php

namespace App\Http\Requests\Backend\Tickets;

trait ValidatesTicketAttachments
{
    /**
     * @return array<string, mixed>
     */
    protected function attachmentRules(): array
    {
        $maxFiles = (int) config('tickets.attachments.max_files', 10);
        $maxKilobytes = (int) config('tickets.attachments.max_kilobytes', 10240);
        $mimes = implode(',', config('tickets.attachments.mimes', ['pdf', 'jpg', 'jpeg', 'png']));

        return [
            'attachments' => ['nullable', 'array', 'max:'.$maxFiles],
            'attachments.*' => ['file', 'max:'.$maxKilobytes, 'mimes:'.$mimes],
        ];
    }
}
