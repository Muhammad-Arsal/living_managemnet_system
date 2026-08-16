<?php

namespace App\Providers;

use App\Repositories\AdminRepository;
use App\Repositories\AuditRepository;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\AuditRepositoryInterface;
use App\Repositories\Contracts\CouncilRepositoryInterface;
use App\Repositories\Contracts\EmailTemplateRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\PropertyTypeRepositoryInterface;
use App\Repositories\Contracts\SiteSettingRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Repositories\Contracts\TenancyRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\TicketPriorityRepositoryInterface;
use App\Repositories\Contracts\TicketRepositoryInterface;
use App\Repositories\Contracts\TicketTypeRepositoryInterface;
use App\Repositories\CouncilRepository;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\PropertyRepository;
use App\Repositories\PropertyTypeRepository;
use App\Repositories\SiteSettingRepository;
use App\Repositories\StaffRepository;
use App\Repositories\TenancyRepository;
use App\Repositories\TenantRepository;
use App\Repositories\TicketPriorityRepository;
use App\Repositories\TicketRepository;
use App\Repositories\TicketTypeRepository;
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
        TicketTypeRepositoryInterface::class => TicketTypeRepository::class,
        TicketPriorityRepositoryInterface::class => TicketPriorityRepository::class,
        TicketRepositoryInterface::class => TicketRepository::class,
        PropertyTypeRepositoryInterface::class => PropertyTypeRepository::class,
        TenantRepositoryInterface::class => TenantRepository::class,
        PropertyRepositoryInterface::class => PropertyRepository::class,
        TenancyRepositoryInterface::class => TenancyRepository::class,
    ];
}
