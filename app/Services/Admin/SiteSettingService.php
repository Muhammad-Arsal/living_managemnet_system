<?php

namespace App\Services\Admin;

use App\Enums\SiteSettingKey;
use App\Repositories\Contracts\SiteSettingRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SiteSettingService
{
    public function __construct(
        private readonly SiteSettingRepositoryInterface $siteSettingRepository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function allKeyed(): array
    {
        return $this->siteSettingRepository->allKeyed();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(array $data): void
    {
        foreach (SiteSettingKey::textKeys() as $key) {
            if (array_key_exists($key->value, $data)) {
                $this->siteSettingRepository->setValue($key->value, $data[$key->value]);
            }
        }

        if (! empty($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $this->storeImage(SiteSettingKey::Logo, $data['logo']);
        }

        if (! empty($data['favicon']) && $data['favicon'] instanceof UploadedFile) {
            $this->storeImage(SiteSettingKey::Favicon, $data['favicon']);
        }
    }

    private function storeImage(SiteSettingKey $key, UploadedFile $file): void
    {
        $previous = $this->siteSettingRepository->getValue($key->value);

        if (! empty($previous) && Storage::disk('public_uploads')->exists($previous)) {
            Storage::disk('public_uploads')->delete($previous);
        }

        $path = $file->store('settings', 'public_uploads');
        $this->siteSettingRepository->setValue($key->value, $path);
    }
}
