<?php

namespace App\Providers;

use App\Repositories\AdminRepository;
use App\Repositories\AuditRepository;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\AuditRepositoryInterface;
use App\Repositories\Contracts\CouncilRepositoryInterface;
use App\Repositories\Contracts\EmailTemplateRepositoryInterface;
use App\Repositories\Contracts\SiteSettingRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Repositories\CouncilRepository;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\SiteSettingRepository;
use App\Repositories\StaffRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        AdminRepositoryInterface::class => AdminRepository::class,
        StaffRepositoryInterface::class => StaffRepository::class,
        CouncilRepositoryInterface::class => CouncilRepository::class,
        EmailTemplateRepositoryInterface::class => EmailTemplateRepository::class,
        SiteSettingRepositoryInterface::class => SiteSettingRepository::class,
        AuditRepositoryInterface::class => AuditRepository::class,
    ];
}
