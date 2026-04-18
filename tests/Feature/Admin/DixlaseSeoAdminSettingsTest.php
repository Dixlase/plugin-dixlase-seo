<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
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
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\BaseSettingsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\CrawlerSettingsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\ExternalSettingsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\IntegrationsController;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Tests\TestCase;

/**
 * SEO設定管理画面（4ページ分割版）のフィーチャーテスト
 *
 * - 基本設定（base）
 * - サイトマップ（crawler）
 * - 外部サービス連携（external）
 * - プラグイン連携（integrations）
 */
class DixlaseSeoAdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private Member $admin;

    private string $baseUrl;

    private string $crawlerUrl;

    private string $externalUrl;

    private string $integrationsUrl;

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
        $this->baseUrl = "/{$adminUrl}/seo/base";
        $this->crawlerUrl = "/{$adminUrl}/seo/crawler";
        $this->externalUrl = "/{$adminUrl}/seo/external";
        $this->integrationsUrl = "/{$adminUrl}/seo/integrations";

        $router = app('router');
        $router->prefix($adminUrl)
            ->middleware(['web', 'auth:member'])
            ->group(function () use ($router) {
                $router->prefix('seo')
                    ->name('dixlase-seo::admin.seo.')
                    ->group(function () use ($router) {
                        $router->get('base', [BaseSettingsController::class, 'show'])->name('base');
                        $router->patch('base', [BaseSettingsController::class, 'update'])->name('base.update');
                        $router->get('crawler', [CrawlerSettingsController::class, 'show'])->name('crawler');
                        $router->patch('crawler', [CrawlerSettingsController::class, 'update'])->name('crawler.update');
                        $router->get('external', [ExternalSettingsController::class, 'show'])->name('external');
                        $router->patch('external', [ExternalSettingsController::class, 'update'])->name('external.update');
                        $router->get('integrations', [IntegrationsController::class, 'show'])->name('integrations');
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
     * @return array<string, mixed>
     */
    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'title_separator' => '|',
            'default_description' => '',
            'default_ogp_image' => '',
            'twitter_card_type' => 'summary_large_image',
            'twitter_site' => '',
            'organization_name' => '',
            'organization_logo' => '',
            'organization_url' => '',
        ], $overrides);
    }

    /**
     * @return array<string, mixed>
     */
    private function crawlerPayload(array $overrides = []): array
    {
        return array_merge([
            'sitemap_enabled' => '1',
            'sitemap_changefreq' => 'weekly',
            'sitemap_priority' => '0.5',
            'robots_txt_mode' => 'auto',
            'robots_txt' => '',
        ], $overrides);
    }

    /**
     * @return array<string, mixed>
     */
    private function externalPayload(array $overrides = []): array
    {
        return array_merge([
            'google_analytics_id' => '',
            'google_site_verification' => '',
        ], $overrides);
    }

    // ========================================
    // 認証・認可テスト
    // ========================================

    public function test_guest_is_redirected_from_base_page(): void
    {
        $this->get($this->baseUrl)->assertRedirect();
    }

    public function test_guest_is_redirected_from_crawler_page(): void
    {
        $this->get($this->crawlerUrl)->assertRedirect();
    }

    public function test_guest_is_redirected_from_external_page(): void
    {
        $this->get($this->externalUrl)->assertRedirect();
    }

    public function test_guest_is_redirected_from_integrations_page(): void
    {
        $this->get($this->integrationsUrl)->assertRedirect();
    }

    public function test_guest_cannot_update_base_settings(): void
    {
        $this->patch($this->baseUrl, $this->basePayload())->assertRedirect();
    }

    public function test_guest_cannot_update_crawler_settings(): void
    {
        $this->patch($this->crawlerUrl, $this->crawlerPayload())->assertRedirect();
    }

    public function test_guest_cannot_update_external_settings(): void
    {
        $this->patch($this->externalUrl, $this->externalPayload())->assertRedirect();
    }

    // ========================================
    // 基本設定ページ
    // ========================================

    public function test_admin_can_access_base_page(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->baseUrl);

        $response->assertOk();
        $response->assertViewIs('dixlase-seo::admin.base');
    }

    public function test_base_page_has_required_view_data(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->baseUrl);

        $response->assertViewHas('settings');
        $response->assertViewHas('twitterCardOptions');
        $response->assertViewHas('coreSiteDescription');
    }

    public function test_twitter_card_options_contain_expected_values(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->baseUrl);

        $values = array_column($response->viewData('twitterCardOptions'), 'value');
        $this->assertContains('summary', $values);
        $this->assertContains('summary_large_image', $values);
    }

    public function test_admin_can_save_base_settings(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->baseUrl, $this->basePayload(['title_separator' => '-']))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('-', DixlaseSeoSetting::getValue('title_separator'));
    }

    public function test_can_save_default_description(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->baseUrl, $this->basePayload(['default_description' => 'サイトの説明']));

        $this->assertSame('サイトの説明', DixlaseSeoSetting::getValue('default_description'));
    }

    public function test_can_save_twitter_card_type_summary(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->baseUrl, $this->basePayload(['twitter_card_type' => 'summary']));

        $this->assertSame('summary', DixlaseSeoSetting::getValue('twitter_card_type'));
    }

    public function test_base_settings_are_loaded_from_database(): void
    {
        DixlaseSeoSetting::setValue('title_separator', '>>');

        $response = $this->actingAs($this->admin, 'member')->get($this->baseUrl);

        $this->assertSame('>>', $response->viewData('settings')['title_separator']);
    }

    public function test_invalid_twitter_card_type_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->baseUrl, $this->basePayload(['twitter_card_type' => 'invalid']))
            ->assertSessionHasErrors('twitter_card_type');
    }

    public function test_invalid_organization_url_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->baseUrl, $this->basePayload(['organization_url' => 'not-a-url']))
            ->assertSessionHasErrors('organization_url');
    }

    // ========================================
    // サイトマップ・robots.txt ページ
    // ========================================

    public function test_admin_can_access_crawler_page(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->crawlerUrl);

        $response->assertOk();
        $response->assertViewIs('dixlase-seo::admin.crawler');
    }

    public function test_crawler_page_has_required_view_data(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->crawlerUrl);

        $response->assertViewHas('settings');
        $response->assertViewHas('robotsTxtModeOptions');
        $response->assertViewHas('changefreqOptions');
    }

    public function test_robots_txt_mode_options_contain_expected_values(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->crawlerUrl);

        $values = array_column($response->viewData('robotsTxtModeOptions'), 'value');
        $this->assertContains('auto', $values);
        $this->assertContains('custom', $values);
    }

    public function test_changefreq_options_contain_all_expected_values(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->crawlerUrl);

        $options = $response->viewData('changefreqOptions');
        foreach (['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'] as $key) {
            $this->assertArrayHasKey($key, $options);
        }
    }

    public function test_admin_can_save_crawler_settings(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->crawlerUrl, $this->crawlerPayload(['sitemap_changefreq' => 'daily']))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('daily', DixlaseSeoSetting::getValue('sitemap_changefreq'));
    }

    public function test_sitemap_enabled_toggle_off_saves_zero(): void
    {
        $payload = $this->crawlerPayload();
        unset($payload['sitemap_enabled']);

        $this->actingAs($this->admin, 'member')->patch($this->crawlerUrl, $payload);

        $this->assertSame('0', DixlaseSeoSetting::getValue('sitemap_enabled'));
    }

    public function test_can_save_custom_robots_txt(): void
    {
        $custom = "User-agent: *\nDisallow: /admin/";
        $this->actingAs($this->admin, 'member')
            ->patch($this->crawlerUrl, $this->crawlerPayload([
                'robots_txt_mode' => 'custom',
                'robots_txt' => $custom,
            ]));

        $this->assertSame('custom', DixlaseSeoSetting::getValue('robots_txt_mode'));
        $this->assertSame($custom, DixlaseSeoSetting::getValue('robots_txt'));
    }

    public function test_invalid_robots_txt_mode_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->crawlerUrl, $this->crawlerPayload(['robots_txt_mode' => 'invalid']))
            ->assertSessionHasErrors('robots_txt_mode');
    }

    public function test_invalid_sitemap_changefreq_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->crawlerUrl, $this->crawlerPayload(['sitemap_changefreq' => 'invalid']))
            ->assertSessionHasErrors('sitemap_changefreq');
    }

    public function test_sitemap_priority_below_zero_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->crawlerUrl, $this->crawlerPayload(['sitemap_priority' => '-0.1']))
            ->assertSessionHasErrors('sitemap_priority');
    }

    public function test_sitemap_priority_above_one_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->crawlerUrl, $this->crawlerPayload(['sitemap_priority' => '1.5']))
            ->assertSessionHasErrors('sitemap_priority');
    }

    // ========================================
    // 外部サービス連携 ページ
    // ========================================

    public function test_admin_can_access_external_page(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->externalUrl);

        $response->assertOk();
        $response->assertViewIs('dixlase-seo::admin.external');
    }

    public function test_admin_can_save_external_settings(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->externalUrl, $this->externalPayload([
                'google_analytics_id' => 'G-ABC123',
                'google_site_verification' => 'verifycode',
            ]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('G-ABC123', DixlaseSeoSetting::getValue('google_analytics_id'));
        $this->assertSame('verifycode', DixlaseSeoSetting::getValue('google_site_verification'));
    }

    public function test_invalid_google_analytics_id_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->externalUrl, $this->externalPayload(['google_analytics_id' => 'invalid-id']))
            ->assertSessionHasErrors('google_analytics_id');
    }

    // ========================================
    // プラグイン連携 ページ
    // ========================================

    public function test_admin_can_access_integrations_page(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->integrationsUrl);

        $response->assertOk();
        $response->assertViewIs('dixlase-seo::admin.integrations');
    }

    public function test_integrations_page_has_seo_meta_plugin_slugs(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->integrationsUrl);

        $response->assertViewHas('seoMetaPluginSlugs');
        $this->assertIsArray($response->viewData('seoMetaPluginSlugs'));
    }
}
