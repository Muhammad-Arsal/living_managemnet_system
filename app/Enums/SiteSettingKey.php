<?php

namespace App\Enums;

enum SiteSettingKey: string
{
    case AdminEmail = 'admin_email';
    case FromEmail = 'from_email';
    case SiteName = 'site_name';
    case Address = 'address';
    case Phone = 'phone';
    case Logo = 'logo';
    case Favicon = 'favicon';

    public function label(): string
    {
        return match ($this) {
            self::AdminEmail => 'Admin Email',
            self::FromEmail => 'From Email',
            self::SiteName => 'Site Name',
            self::Address => 'Address',
            self::Phone => 'Phone',
            self::Logo => 'Logo',
            self::Favicon => 'Favicon',
        };
    }

    /**
     * @return list<self>
     */
    public static function textKeys(): array
    {
        return [
            self::AdminEmail,
            self::FromEmail,
            self::SiteName,
            self::Address,
            self::Phone,
        ];
    }
}
