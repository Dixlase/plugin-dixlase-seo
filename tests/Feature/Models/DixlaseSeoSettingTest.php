<?php

/**
 * This file is part of DixlaseSEO.
 *
 * Copyright (C) 2026
 * https://example.com
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

namespace Plugins\DixlaseSEO\Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Tests\TestCase;

/**
 * DixlaseSeoSetting モデルのフィーチャーテスト
 */
class DixlaseSeoSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // プラグインのマイグレーションを実行
        $this->artisan('migrate', [
            '--path' => base_path('plugins/DixlaseSEO/database/migrations'),
            '--realpath' => true,
        ]);
    }

    /**
     * 存在しないキーに対してデフォルト値（null）を返すこと
     */
    public function test_get_value_returns_null_by_default_when_key_not_found(): void
    {
        $result = DixlaseSeoSetting::getValue('nonexistent_key');

        $this->assertNull($result);
    }

    /**
     * 存在しないキーに対して指定したデフォルト値を返すこと
     */
    public function test_get_value_returns_custom_default_when_key_not_found(): void
    {
        $result = DixlaseSeoSetting::getValue('nonexistent_key', 'fallback_value');

        $this->assertSame('fallback_value', $result);
    }

    /**
     * setValue で新しい設定を作成できること
     */
    public function test_set_value_creates_new_setting(): void
    {
        DixlaseSeoSetting::setValue('test_key', 'test_value');

        $this->assertDatabaseHas('dls_plg_dixlase_seo_settings', [
            'name' => 'test_key',
            'value' => 'test_value',
        ]);
    }

    /**
     * setValue で保存した値を getValue で取得できること
     */
    public function test_get_value_returns_saved_value(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $this->assertSame('1', DixlaseSeoSetting::getValue('sitemap_enabled'));
    }

    /**
     * setValue で既存の設定を上書きできること（updateOrCreate）
     */
    public function test_set_value_updates_existing_setting(): void
    {
        DixlaseSeoSetting::setValue('title_separator', '|');
        DixlaseSeoSetting::setValue('title_separator', '-');

        $this->assertSame('-', DixlaseSeoSetting::getValue('title_separator'));
        // 重複レコードが作成されないこと
        $this->assertSame(1, DixlaseSeoSetting::where('name', 'title_separator')->count());
    }

    /**
     * setValue で null 値を保存できること
     */
    public function test_set_value_can_store_null(): void
    {
        DixlaseSeoSetting::setValue('default_description', null);

        $this->assertNull(DixlaseSeoSetting::getValue('default_description', 'should_not_return'));
    }

    /**
     * setValue で空文字列を保存できること
     */
    public function test_set_value_can_store_empty_string(): void
    {
        DixlaseSeoSetting::setValue('twitter_site', '');

        $this->assertSame('', DixlaseSeoSetting::getValue('twitter_site', 'default'));
    }

    /**
     * setMany で複数の設定を一括保存できること
     */
    public function test_set_many_saves_multiple_settings(): void
    {
        DixlaseSeoSetting::setMany([
            'title_separator' => '|',
            'default_description' => 'サイトの説明文',
            'twitter_card_type' => 'summary_large_image',
        ]);

        $this->assertSame('|', DixlaseSeoSetting::getValue('title_separator'));
        $this->assertSame('サイトの説明文', DixlaseSeoSetting::getValue('default_description'));
        $this->assertSame('summary_large_image', DixlaseSeoSetting::getValue('twitter_card_type'));
    }

    /**
     * setMany で既存の設定を更新できること
     */
    public function test_set_many_updates_existing_settings(): void
    {
        DixlaseSeoSetting::setValue('title_separator', '|');

        DixlaseSeoSetting::setMany([
            'title_separator' => '-',
            'sitemap_enabled' => '0',
        ]);

        $this->assertSame('-', DixlaseSeoSetting::getValue('title_separator'));
        $this->assertSame('0', DixlaseSeoSetting::getValue('sitemap_enabled'));
    }

    /**
     * getMany が複数の設定を一括取得できること
     */
    public function test_get_many_returns_multiple_settings(): void
    {
        DixlaseSeoSetting::setValue('title_separator', '|');
        DixlaseSeoSetting::setValue('default_description', 'テスト説明文');

        $result = DixlaseSeoSetting::getMany([
            'title_separator' => '/',
            'default_description' => '',
        ]);

        $this->assertSame('|', $result['title_separator']);
        $this->assertSame('テスト説明文', $result['default_description']);
    }

    /**
     * getMany で存在しないキーはデフォルト値を返すこと
     */
    public function test_get_many_returns_defaults_for_missing_keys(): void
    {
        $result = DixlaseSeoSetting::getMany([
            'nonexistent_key1' => 'default1',
            'nonexistent_key2' => 'default2',
        ]);

        $this->assertSame('default1', $result['nonexistent_key1']);
        $this->assertSame('default2', $result['nonexistent_key2']);
    }

    /**
     * getMany が保存値とデフォルト値を混在して正しく返すこと
     */
    public function test_get_many_mixes_saved_and_default_values(): void
    {
        DixlaseSeoSetting::setValue('title_separator', '>>');

        $result = DixlaseSeoSetting::getMany([
            'title_separator' => '|',
            'nonexistent_key' => 'my_default',
        ]);

        $this->assertSame('>>', $result['title_separator']);
        $this->assertSame('my_default', $result['nonexistent_key']);
    }

    /**
     * getMany が空の配列でも動作すること
     */
    public function test_get_many_handles_empty_defaults(): void
    {
        $result = DixlaseSeoSetting::getMany([]);

        $this->assertSame([], $result);
    }

    /**
     * テーブル名が正しく設定されていること
     */
    public function test_model_uses_correct_table_name(): void
    {
        $model = new DixlaseSeoSetting();

        $this->assertSame('dls_plg_dixlase_seo_settings', $model->getTable());
    }

    /**
     * name フィールドがユニーク制約を持つこと
     */
    public function test_name_field_has_unique_constraint(): void
    {
        DixlaseSeoSetting::create(['name' => 'unique_key', 'value' => 'value1']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DixlaseSeoSetting::create(['name' => 'unique_key', 'value' => 'value2']);
    }
}
