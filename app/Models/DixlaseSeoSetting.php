<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
 *
 * Dixlase SEO is dual-licensed. You may use this file under either:
 *
 *   (a) the GNU General Public License version 3 or later, as published
 *       by the Free Software Foundation; or
 *
 *   (b) a commercial license agreement obtained from exc-D inc.
 *
 * Unless you have entered into a commercial license agreement, this
 * file is governed by the GPL terms below.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Plugins\DixlaseSEO\App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SEO設定のキーバリュー型モデル
 */
class DixlaseSeoSetting extends Model
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'dls_plg_dixlase_seo_settings';

    /**
     * 一括代入可能な属性
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'value'];

    /**
     * 設定値を取得する
     */
    public static function getValue(string $name, mixed $default = null): mixed
    {
        $setting = self::where('name', $name)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * 設定値を保存する
     */
    public static function setValue(string $name, mixed $value): void
    {
        self::updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        );
    }

    /**
     * 複数の設定値を一括保存する
     *
     * @param array<string, mixed> $settings
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            self::setValue($key, $value);
        }
    }

    /**
     * 複数の設定値を一括取得する
     *
     * @param array<string, mixed> $defaults キーとデフォルト値のペア
     * @return array<string, mixed>
     */
    public static function getMany(array $defaults): array
    {
        $names = array_keys($defaults);
        $settings = self::whereIn('name', $names)->pluck('value', 'name')->toArray();

        $result = [];
        foreach ($defaults as $key => $default) {
            $result[$key] = $settings[$key] ?? $default;
        }

        return $result;
    }
}
