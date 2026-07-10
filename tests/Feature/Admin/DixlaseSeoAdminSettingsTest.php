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

namespace Plugins\DixlaseSEO\Tests\Feature\Admin;

use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\BaseSettingsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\ExternalSettingsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\IntegrationsController;
use Plugins\DixlaseSEO\App\Http\Controllers\Admin\SitemapSettingsController;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoMeta;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Tests\TestCase;

/**
 * SEO設定管理画面（4ページ分割版）のフィーチャーテスト
 *
 * - 基本設定（base）
 * - サイトマップ（sitemap）
 * - 外部サービス連携（external）
 * - プラグイン連携（integrations）
 */
class DixlaseSeoAdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private Member $admin;

    private Member $superAdmin;

    private string $baseUrl;

    private string $sitemapUrl;

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
        $this->sitemapUrl = "/{$adminUrl}/seo/sitemap";
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
                        $router->get('sitemap', [SitemapSettingsController::class, 'show'])->name('sitemap');
                        $router->patch('sitemap', [SitemapSettingsController::class, 'update'])->name('sitemap.update');
                        $router->get('external', [ExternalSettingsController::class, 'show'])->name('external');
                        $router->patch('external', [ExternalSettingsController::class, 'update'])->name('external.update');
                        $router->get('integrations', [IntegrationsController::class, 'show'])->name('integrations');
                        $router->patch('integrations', [IntegrationsController::class, 'update'])->name('integrations.update');
                        $router->post('integrations/purge-orphans', [IntegrationsController::class, 'purgeOrphans'])
                            ->name('integrations.purge-orphans');
                    });
            });

        $router->getRoutes()->refreshNameLookups();
        $router->getRoutes()->refreshActionLookups();

        // Admin user — allowed to view external/base/sitemap/integrations
        // pages, but NOT allowed to save the external-settings form (edit
        // is restricted to SUPER_ADMIN — see c66dd2f).
        $this->admin = Member::create([
            'account_name' => 'testadmin',
            'display_name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => MemberRole::ADMIN,
            'status' => MemberStatus::Active,
        ]);

        // Super-admin user — required for editing external settings (Google
        // Analytics ID + Search Console verification token, both emitted
        // into public HTML). Used only by the tests that exercise the
        // super-admin-only save path.
        $this->superAdmin = Member::create([
            'account_name' => 'testsuperadmin',
            'display_name' => 'Test Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => MemberRole::SUPER_ADMIN,
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
    private function sitemapPayload(array $overrides = []): array
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

    public function test_guest_is_redirected_from_sitemap_page(): void
    {
        $this->get($this->sitemapUrl)->assertRedirect();
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

    public function test_guest_cannot_update_sitemap_settings(): void
    {
        $this->patch($this->sitemapUrl, $this->sitemapPayload())->assertRedirect();
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

    public function test_admin_can_access_sitemap_page(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->sitemapUrl);

        $response->assertOk();
        $response->assertViewIs('dixlase-seo::admin.sitemap');
    }

    public function test_sitemap_page_has_required_view_data(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->sitemapUrl);

        $response->assertViewHas('settings');
        $response->assertViewHas('robotsTxtModeOptions');
        $response->assertViewHas('changefreqOptions');
    }

    public function test_robots_txt_mode_options_contain_expected_values(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->sitemapUrl);

        $values = array_column($response->viewData('robotsTxtModeOptions'), 'value');
        $this->assertContains('auto', $values);
        $this->assertContains('custom', $values);
    }

    public function test_changefreq_options_contain_all_expected_values(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->sitemapUrl);

        $options = $response->viewData('changefreqOptions');
        foreach (['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'] as $key) {
            $this->assertArrayHasKey($key, $options);
        }
    }

    public function test_admin_can_save_sitemap_settings(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->sitemapUrl, $this->sitemapPayload(['sitemap_changefreq' => 'daily']))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('daily', DixlaseSeoSetting::getValue('sitemap_changefreq'));
    }

    public function test_sitemap_enabled_toggle_off_saves_zero(): void
    {
        $payload = $this->sitemapPayload();
        unset($payload['sitemap_enabled']);

        $this->actingAs($this->admin, 'member')->patch($this->sitemapUrl, $payload);

        $this->assertSame('0', DixlaseSeoSetting::getValue('sitemap_enabled'));
    }

    public function test_can_save_custom_robots_txt(): void
    {
        $custom = "User-agent: *\nDisallow: /admin/";
        $this->actingAs($this->admin, 'member')
            ->patch($this->sitemapUrl, $this->sitemapPayload([
                'robots_txt_mode' => 'custom',
                'robots_txt' => $custom,
            ]));

        $this->assertSame('custom', DixlaseSeoSetting::getValue('robots_txt_mode'));
        $this->assertSame($custom, DixlaseSeoSetting::getValue('robots_txt'));
    }

    public function test_invalid_robots_txt_mode_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->sitemapUrl, $this->sitemapPayload(['robots_txt_mode' => 'invalid']))
            ->assertSessionHasErrors('robots_txt_mode');
    }

    public function test_invalid_sitemap_changefreq_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->sitemapUrl, $this->sitemapPayload(['sitemap_changefreq' => 'invalid']))
            ->assertSessionHasErrors('sitemap_changefreq');
    }

    public function test_sitemap_priority_below_zero_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->sitemapUrl, $this->sitemapPayload(['sitemap_priority' => '-0.1']))
            ->assertSessionHasErrors('sitemap_priority');
    }

    public function test_sitemap_priority_above_one_fails_validation(): void
    {
        $this->actingAs($this->admin, 'member')
            ->patch($this->sitemapUrl, $this->sitemapPayload(['sitemap_priority' => '1.5']))
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

    public function test_super_admin_can_save_external_settings(): void
    {
        // External settings edit is restricted to SUPER_ADMIN by
        // ExternalSettingsController::authorizeEdit — the values here
        // (Google Analytics ID + Search Console verification token) are
        // emitted into public HTML, so we deliberately raise the bar.
        // See c66dd2f for the enforcement change.
        $this->actingAs($this->superAdmin, 'member')
            ->patch($this->externalUrl, $this->externalPayload([
                'google_analytics_id' => 'G-ABC123',
                'google_site_verification' => 'verifycode',
            ]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('G-ABC123', DixlaseSeoSetting::getValue('google_analytics_id'));
        $this->assertSame('verifycode', DixlaseSeoSetting::getValue('google_site_verification'));
    }

    public function test_admin_cannot_save_external_settings(): void
    {
        // Regression guard for c66dd2f: an ADMIN role must NOT be able to
        // save the external-settings form, even though they can still
        // view the page. ExternalSettingsController::authorizeEdit is the
        // enforcement point — this test would 302-redirect (assertRedirect)
        // if the controller regressed to the old admin-can-edit behaviour.
        // The declared setting must also stay at the pre-request default
        // (empty string), proving nothing was written to the DB.
        $this->actingAs($this->admin, 'member')
            ->patch($this->externalUrl, $this->externalPayload([
                'google_analytics_id' => 'G-SHOULDNOTPERSIST',
                'google_site_verification' => 'nope',
            ]))
            ->assertForbidden();

        $this->assertSame('', DixlaseSeoSetting::getValue('google_analytics_id', ''));
        $this->assertSame('', DixlaseSeoSetting::getValue('google_site_verification', ''));
    }

    public function test_invalid_google_analytics_id_fails_validation(): void
    {
        // Validation runs before ExternalSettingsController::authorizeEdit
        // (FormRequest validation happens during resolution, before the
        // controller method body executes), so an admin submitting invalid
        // input still gets a validation redirect — not a 403. This test
        // pins that ordering so a controller-level pre-validation authz
        // check does not silently hide validation errors from operators.
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

    public function test_integrations_page_has_plugins_and_orphans_view_data(): void
    {
        $response = $this->actingAs($this->admin, 'member')->get($this->integrationsUrl);

        $response->assertViewHas('plugins');
        $response->assertViewHas('orphans');
        $this->assertIsArray($response->viewData('plugins'));
        $this->assertIsArray($response->viewData('orphans'));
        $this->assertArrayHasKey('count', $response->viewData('orphans'));
        $this->assertArrayHasKey('byPlugin', $response->viewData('orphans'));
    }

    public function test_update_integrations_saves_enabled_toggle(): void
    {
        // seo-metaプラグインが存在しない環境では toggles は空でも200応答
        $this->actingAs($this->admin, 'member')
            ->patch($this->integrationsUrl, ['integration' => []])
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_guest_cannot_update_integrations(): void
    {
        $this->patch($this->integrationsUrl, ['integration' => []])->assertRedirect();
    }

    public function test_purge_orphans_removes_only_unsupported_plugin_records(): void
    {
        // 孤立（非対応プラグイン）のレコードを作成
        DixlaseSeoMeta::create([
            'plugin_slug' => 'nonexistent-plugin',
            'entity_id' => '1',
            'description' => 'orphan desc',
        ]);

        $purgeUrl = $this->integrationsUrl.'/purge-orphans';
        $this->actingAs($this->admin, 'member')
            ->post($purgeUrl)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(0, DixlaseSeoMeta::where('plugin_slug', 'nonexistent-plugin')->count());
    }

    public function test_guest_cannot_purge_orphans(): void
    {
        $this->post($this->integrationsUrl.'/purge-orphans')->assertRedirect();
    }
}
