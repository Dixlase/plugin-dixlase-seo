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

namespace Plugins\DixlaseSEO\Tests\Feature\Admin;

use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\DixlaseSeoAdminSettingsController;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Tests\TestCase;

/**
 * SEO設定管理画面のフィーチャーテスト
 */
class DixlaseSeoAdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    /** @var Member */
    private Member $admin;

    /** @var string */
    private string $indexUrl;

    /** @var string */
    private string $updateUrl;

    protected function setUp(): void
    {
        // インストール済みとして扱う
        $_ENV['INSTALLED'] = 'true';
        $_SERVER['INSTALLED'] = 'true';

        // App\Http\Controllers\Controller が存在しない場合に定義する
        if (! class_exists('App\Http\Controllers\Controller')) {
            eval('namespace App\Http\Controllers; abstract class Controller extends \Illuminate\Routing\Controller {}');
        }

        parent::setUp();

        // プラグイン設定を手動ロード
        config(['dixlase_seo' => require base_path('plugins/DixlaseSEO/config/dixlase_seo.php')]);

        // ビューと翻訳を手動登録
        $this->app['view']->addNamespace('dixlase-seo', base_path('plugins/DixlaseSEO/resources/views'));
        $this->app['translator']->addNamespace('dixlase-seo', base_path('plugins/DixlaseSEO/lang'));

        // プラグインのマイグレーションを実行
        Artisan::call('migrate', [
            '--path' => 'plugins/DixlaseSEO/database/migrations',
            '--realpath' => false,
        ]);

        // ルートを手動登録
        $adminUrl = config('admin.admin_url', 'admin');
        $this->indexUrl = "/{$adminUrl}/dixlase-seo/settings";
        $this->updateUrl = "/{$adminUrl}/dixlase-seo/settings";

        $router = app('router');
        $router->prefix($adminUrl)
            ->middleware(['web', 'auth:member'])
            ->group(function () use ($router) {
                $router->prefix('dixlase-seo')
                    ->name('dixlase-seo::admin.dixlase-seo.')
                    ->group(function () use ($router) {
                        $router->get('settings', [DixlaseSeoAdminSettingsController::class, 'settings'])
                            ->name('settings');
                        $router->patch('settings', [DixlaseSeoAdminSettingsController::class, 'updateSettings'])
                            ->name('settings.update');
                    });
            });

        $router->getRoutes()->refreshNameLookups();
        $router->getRoutes()->refreshActionLookups();

        // 管理者ユーザーを作成
        $this->admin = Member::create([
            'account_name' => 'testadmin',
            'display_name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => MemberRole::ADMIN,
            'status' => MemberStatus::Active,
        ]);
    }

    protected function tearDown(): void
    {
        $_ENV['INSTALLED'] = 'false';
        $_SERVER['INSTALLED'] = 'false';
        parent::tearDown();
    }

    /**
     * 有効なデフォルトリクエストデータを返すヘルパー
     *
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'title_separator' => '|',
            'default_description' => '',
            'default_ogp_image' => '',
            'twitter_card_type' => 'summary_large_image',
            'twitter_site' => '',
            'organization_name' => '',
            'organization_logo' => '',
            'organization_url' => '',
            'sitemap_enabled' => '1',
            'sitemap_changefreq' => 'weekly',
            'sitemap_priority' => '0.5',
            'robots_txt_mode' => 'auto',
            'robots_txt' => '',
        ];
    }

    // ========================================
    // 認証・認可テスト
    // ========================================

    /**
     * 未ログインユーザーは設定画面にアクセスするとリダイレクトされること
     */
    public function test_guest_is_redirected_from_settings_page(): void
    {
        $response = $this->get($this->indexUrl);

        $response->assertRedirect();
    }

    /**
     * 未ログインユーザーがPATCHリクエストを送るとリダイレクトされること
     */
    public function test_guest_cannot_update_settings(): void
    {
        $response = $this->patch($this->updateUrl, $this->validPayload());

        $response->assertRedirect();
    }

    // ========================================
    // 設定画面表示テスト
    // ========================================

    /**
     * 管理者が設定画面にアクセスできること
     */
    public function test_admin_can_access_settings_page(): void
    {
        $response = $this->actingAs($this->admin, 'member')
            ->get($this->indexUrl);

        $response->assertOk();
        $response->assertViewIs('dixlase-seo::admin.settings.index');
    }

    /**
     * 設定画面に必要なビュー変数が渡されること
     */
    public function test_settings_page_has_required_view_data(): void
    {
        $response = $this->actingAs($this->admin, 'member')
            ->get($this->indexUrl);

        $response->assertViewHas('settings');
        $response->assertViewHas('twitterCardOptions');
        $response->assertViewHas('robotsTxtModeOptions');
        $response->assertViewHas('changefreqOptions');
    }

    /**
     * twitterCardOptions に summary と summary_large_image が含まれること
     */
    public function test_twitter_card_options_contain_expected_values(): void
    {
        $response = $this->actingAs($this->admin, 'member')
            ->get($this->indexUrl);

        $options = $response->viewData('twitterCardOptions');
        $values = array_column($options, 'value');

        $this->assertContains('summary', $values);
        $this->assertContains('summary_large_image', $values);
    }

    /**
     * robotsTxtModeOptions に auto と custom が含まれること
     */
    public function test_robots_txt_mode_options_contain_expected_values(): void
    {
        $response = $this->actingAs($this->admin, 'member')
            ->get($this->indexUrl);

        $options = $response->viewData('robotsTxtModeOptions');
        $values = array_column($options, 'value');

        $this->assertContains('auto', $values);
        $this->assertContains('custom', $values);
    }

    /**
     * changefreqOptions に全頻度オプションが含まれること
     */
    public function test_changefreq_options_contain_all_expected_values(): void
    {
        $response = $this->actingAs($this->admin, 'member')
            ->get($this->indexUrl);

        $options = $response->viewData('changefreqOptions');

        $this->assertArrayHasKey('always', $options);
        $this->assertArrayHasKey('hourly', $options);
        $this->assertArrayHasKey('daily', $options);
        $this->assertArrayHasKey('weekly', $options);
        $this->assertArrayHasKey('monthly', $options);
        $this->assertArrayHasKey('yearly', $options);
        $this->assertArrayHasKey('never', $options);
    }

    /**
     * 設定値がデータベースの値で渡されること
     */
    public function test_settings_are_loaded_from_database(): void
    {
        DixlaseSeoSetting::setValue('title_separator', '>>');

        $response = $this->actingAs($this->admin, 'member')
            ->get($this->indexUrl);

        $settings = $response->viewData('settings');
        $this->assertSame('>>', $settings['title_separator']);
    }

    // ========================================
    // 設定保存テスト
    // ========================================

    /**
     * 有効なデータで設定を保存できること
     */
    public function test_admin_can_save_settings(): void
    {
        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $this->validPayload());

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * 保存後にセッションにsuccess メッセージが含まれること
     */
    public function test_saving_settings_flashes_success_message(): void
    {
        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $this->validPayload());

        $response->assertSessionHas('success');
    }

    /**
     * タイトル区切り文字を保存できること
     */
    public function test_can_save_title_separator(): void
    {
        $payload = array_merge($this->validPayload(), ['title_separator' => '-']);

        $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $this->assertSame('-', DixlaseSeoSetting::getValue('title_separator'));
    }

    /**
     * デフォルト説明文を保存できること
     */
    public function test_can_save_default_description(): void
    {
        $payload = array_merge($this->validPayload(), ['default_description' => 'このサイトの説明文です。']);

        $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $this->assertSame('このサイトの説明文です。', DixlaseSeoSetting::getValue('default_description'));
    }

    /**
     * Twitter Cardタイプをsummaryに変更できること
     */
    public function test_can_save_twitter_card_type_summary(): void
    {
        $payload = array_merge($this->validPayload(), ['twitter_card_type' => 'summary']);

        $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $this->assertSame('summary', DixlaseSeoSetting::getValue('twitter_card_type'));
    }

    /**
     * サイトマップが有効化できること
     */
    public function test_can_enable_sitemap(): void
    {
        $payload = array_merge($this->validPayload(), ['sitemap_enabled' => '1']);

        $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $this->assertSame('1', DixlaseSeoSetting::getValue('sitemap_enabled'));
    }

    /**
     * サイトマップが無効化できること（チェックボックスOFF = フィールドなし）
     */
    public function test_can_disable_sitemap_when_checkbox_absent(): void
    {
        // まず有効化
        $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $this->validPayload());

        // sitemap_enabled を送信しないで無効化
        $payload = $this->validPayload();
        unset($payload['sitemap_enabled']);

        $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $this->assertSame('0', DixlaseSeoSetting::getValue('sitemap_enabled'));
    }

    /**
     * robots.txtモードをcustomに変更できること
     */
    public function test_can_save_robots_txt_mode_custom(): void
    {
        $payload = array_merge($this->validPayload(), [
            'robots_txt_mode' => 'custom',
            'robots_txt' => "User-agent: *\nDisallow: /admin",
        ]);

        $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $this->assertSame('custom', DixlaseSeoSetting::getValue('robots_txt_mode'));
        $this->assertSame("User-agent: *\nDisallow: /admin", DixlaseSeoSetting::getValue('robots_txt'));
    }

    /**
     * 組織名を保存できること
     */
    public function test_can_save_organization_name(): void
    {
        $payload = array_merge($this->validPayload(), ['organization_name' => '株式会社テスト']);

        $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $this->assertSame('株式会社テスト', DixlaseSeoSetting::getValue('organization_name'));
    }

    // ========================================
    // バリデーションテスト
    // ========================================

    /**
     * タイトル区切り文字が必須であること
     */
    public function test_title_separator_is_required(): void
    {
        $payload = $this->validPayload();
        unset($payload['title_separator']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('title_separator');
    }

    /**
     * タイトル区切り文字が10文字を超えるとバリデーションエラーになること
     */
    public function test_title_separator_max_length_is_10(): void
    {
        $payload = array_merge($this->validPayload(), ['title_separator' => str_repeat('a', 11)]);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('title_separator');
    }

    /**
     * デフォルト説明文が300文字を超えるとバリデーションエラーになること
     */
    public function test_default_description_max_length_is_300(): void
    {
        $payload = array_merge($this->validPayload(), ['default_description' => str_repeat('a', 301)]);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('default_description');
    }

    /**
     * 無効なTwitter Cardタイプがバリデーションエラーになること
     */
    public function test_invalid_twitter_card_type_fails_validation(): void
    {
        $payload = array_merge($this->validPayload(), ['twitter_card_type' => 'invalid_type']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('twitter_card_type');
    }

    /**
     * Twitter Cardタイプが必須であること
     */
    public function test_twitter_card_type_is_required(): void
    {
        $payload = $this->validPayload();
        unset($payload['twitter_card_type']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('twitter_card_type');
    }

    /**
     * 無効なrobots.txtモードがバリデーションエラーになること
     */
    public function test_invalid_robots_txt_mode_fails_validation(): void
    {
        $payload = array_merge($this->validPayload(), ['robots_txt_mode' => 'invalid_mode']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('robots_txt_mode');
    }

    /**
     * robots.txtモードが必須であること
     */
    public function test_robots_txt_mode_is_required(): void
    {
        $payload = $this->validPayload();
        unset($payload['robots_txt_mode']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('robots_txt_mode');
    }

    /**
     * 無効なサイトマップ更新頻度がバリデーションエラーになること
     */
    public function test_invalid_sitemap_changefreq_fails_validation(): void
    {
        $payload = array_merge($this->validPayload(), ['sitemap_changefreq' => 'sometimes']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('sitemap_changefreq');
    }

    /**
     * サイトマップ優先度が0未満でバリデーションエラーになること
     */
    public function test_sitemap_priority_below_zero_fails_validation(): void
    {
        $payload = array_merge($this->validPayload(), ['sitemap_priority' => '-0.1']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('sitemap_priority');
    }

    /**
     * サイトマップ優先度が1超でバリデーションエラーになること
     */
    public function test_sitemap_priority_above_one_fails_validation(): void
    {
        $payload = array_merge($this->validPayload(), ['sitemap_priority' => '1.1']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('sitemap_priority');
    }

    /**
     * 組織URLが不正な形式でバリデーションエラーになること
     */
    public function test_invalid_organization_url_fails_validation(): void
    {
        $payload = array_merge($this->validPayload(), ['organization_url' => 'not-a-url']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('organization_url');
    }

    /**
     * 組織URLがhttpsで始まる場合は有効であること
     */
    public function test_valid_organization_url_passes_validation(): void
    {
        $payload = array_merge($this->validPayload(), ['organization_url' => 'https://example.com']);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionMissing('errors');
    }

    /**
     * robots.txtが10000文字を超えるとバリデーションエラーになること
     */
    public function test_robots_txt_max_length_is_10000(): void
    {
        $payload = array_merge($this->validPayload(), [
            'robots_txt_mode' => 'custom',
            'robots_txt' => str_repeat('a', 10001),
        ]);

        $response = $this->actingAs($this->admin, 'member')
            ->patch($this->updateUrl, $payload);

        $response->assertSessionHasErrors('robots_txt');
    }
}
