<?php

namespace App\Repositories\Contracts;

interface SiteSettingRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function allKeyed(): array;

    public function getValue(string $key, mixed $default = null): mixed;

    public function setValue(string $key, mixed $value): void;
}
