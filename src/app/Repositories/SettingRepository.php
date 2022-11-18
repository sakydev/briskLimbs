<?php declare(strict_types=1);

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository
{
    public function update(string $name, string $value): bool {
        return (new Setting())::where('name', $name)->update(['value' => $value]);
    }
}
