<?php

namespace App\Providers;

use App\Repositories\AdminRepository;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\CouncilRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Repositories\CouncilRepository;
use App\Repositories\StaffRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        AdminRepositoryInterface::class => AdminRepository::class,
        StaffRepositoryInterface::class => StaffRepository::class,
        CouncilRepositoryInterface::class => CouncilRepository::class,
    ];
}
