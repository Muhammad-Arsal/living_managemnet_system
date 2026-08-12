<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EmailTemplate extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'email_type',
        'subject',
        'status',
        'html_content',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public static function getByType(string $emailType): ?self
    {
        return self::query()
            ->where('email_type', $emailType)
            ->where('status', true)
            ->first();
    }

    /**
     * @param  array<string, string>  $replacements
     */
    public function renderSubject(array $replacements = []): string
    {
        return $this->replacePlaceholders($this->subject, $replacements);
    }

    /**
     * @param  array<string, string>  $replacements
     */
    public function renderHtmlContent(array $replacements = []): string
    {
        return $this->replacePlaceholders($this->html_content ?? '', $replacements);
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function replacePlaceholders(string $text, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $text = str_replace(['{{'.$key.'}}', '{{ '.$key.' }}'], (string) $value, $text);
        }

        return $text;
    }
}
