<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_keys(config('portals', [])) as $portal) {
            foreach ($this->templatesForPortal($portal) as $data) {
                EmailTemplate::query()->updateOrCreate(
                    ['email_type' => $data['email_type']],
                    $data
                );
            }
        }

        $this->seedTicketTemplates();
    }

    /**
     * @return list<array{email_type: string, subject: string, status: bool, html_content: string}>
     */
    protected function templatesForPortal(string $portal): array
    {
        $config = config("portals.{$portal}", []);
        $label = strtolower((string) ($config['label'] ?? $portal));
        $templates = $config['templates'] ?? [];

        return [
            [
                'email_type' => $templates['welcome'],
                'subject' => 'Set your password – {{name}}',
                'status' => true,
                'html_content' => '<p>Hello {{name}},</p>'
                    .'<p>A '.$label.' account has been created for you. Please set your password by clicking the link below:</p>'
                    .'<p><a href="{{reset_link}}" style="color: #059669;">Set your password</a></p>'
                    .'<p>If you did not expect this email, you can ignore it.</p>'
                    .'<p>This link will expire in 60 minutes.</p>',
            ],
            [
                'email_type' => $templates['password_setup'],
                'subject' => 'Set your password – {{name}}',
                'status' => true,
                'html_content' => '<p>Hello {{name}},</p>'
                    .'<p>Please set a password for your '.$label.' account ({{email}}) using the link below:</p>'
                    .'<p><a href="{{reset_link}}" style="color: #059669;">Set your password</a></p>'
                    .'<p>This link will expire in 60 minutes.</p>',
            ],
            [
                'email_type' => $templates['password_reset'],
                'subject' => 'Reset your password – {{name}}',
                'status' => true,
                'html_content' => '<p>Hello {{name}},</p>'
                    .'<p>You are receiving this email because we received a password reset request for your '.$label.' account.</p>'
                    .'<p><a href="{{reset_link}}" style="color: #059669;">Reset your password</a></p>'
                    .'<p>If you did not request a password reset, you can ignore this email.</p>'
                    .'<p>This link will expire in 60 minutes.</p>',
            ],
            [
                'email_type' => $templates['email_verification'],
                'subject' => 'Verify your '.$label.' portal email address – {{name}}',
                'status' => true,
                'html_content' => '<p>Hello {{name}},</p>'
                    .'<p>Please verify your email address ({{email}}) for your '.$label.' portal account:</p>'
                    .'<p><a href="{{verification_link}}" style="color: #059669;">Verify email address</a></p>'
                    .'<p>If you did not expect this email, you can ignore it.</p>',
            ],
        ];
    }

    protected function seedTicketTemplates(): void
    {
        $templates = [
            [
                'email_type' => config('tickets.email_types.created'),
                'subject' => 'New ticket assigned: {{ticket_reference}}',
                'status' => true,
                'html_content' => '<p>Hello {{name}},</p>'
                    .'<p>{{sender_name}} created ticket <strong>{{ticket_reference}}</strong> ({{ticket_subject}}).</p>'
                    .'<p>Type: {{ticket_type}} · Priority: {{ticket_priority}}</p>'
                    .'<p>{{message_excerpt}}</p>'
                    .'<p><a href="{{ticket_url}}" style="color: #059669;">View ticket</a></p>',
            ],
            [
                'email_type' => config('tickets.email_types.replied'),
                'subject' => 'New reply on {{ticket_reference}}',
                'status' => true,
                'html_content' => '<p>Hello {{name}},</p>'
                    .'<p>{{sender_name}} replied to ticket <strong>{{ticket_reference}}</strong> ({{ticket_subject}}).</p>'
                    .'<p>{{message_excerpt}}</p>'
                    .'<p><a href="{{ticket_url}}" style="color: #059669;">View ticket</a></p>',
            ],
        ];

        foreach ($templates as $data) {
            EmailTemplate::query()->updateOrCreate(
                ['email_type' => $data['email_type']],
                $data
            );
        }
    }
}
