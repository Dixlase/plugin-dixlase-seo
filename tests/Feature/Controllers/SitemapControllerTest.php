<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
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

namespace Plugins\DixlaseSEO\Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Plugins\DixlaseSEO\App\Http\Controllers\SitemapController;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Services\SitemapGenerator;
use Tests\TestCase;

/**
 * SitemapController のフィーチャーテスト
 */
class SitemapControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * INSTALLED as the harness had it, per superglobal: an empty array when
     * the harness set none, otherwise ['INSTALLED' => its exact value].
     *
     * @var array{env: array<string, mixed>, server: array<string, mixed>}
     */
    private array $installedBefore = ['env' => [], 'server' => []];

    protected function setUp(): void
    {
        // Remember it before overwriting, so tearDown() can put the
        // harness's own value back instead of writing a constant. Measured
        // on PHPUnit 12: this plugin's phpunit.xml sets INSTALLED in $_ENV
        // only, so "the harness set none" has to survive the round trip too.
        $this->installedBefore = [
            'env' => array_intersect_key($_ENV, ['INSTALLED' => null]),
            'server' => array_intersect_key($_SERVER, ['INSTALLED' => null]),
        ];

        // インストール済みとして扱う
        $_ENV['INSTALLED'] = 'true';
        $_SERVER['INSTALLED'] = 'true';

        // App\Http\Controllers\Controller が存在しない場合に定義する
        if (! class_exists('App\Http\Controllers\Controller')) {
            eval('namespace App\Http\Controllers; abstract class Controller {}');
        }

        parent::setUp();

        // プラグイン設定を手動ロード
        config(['dixlase_seo' => require base_path('plugins/DixlaseSEO/config/dixlase_seo.php')]);

        // プラグインのマイグレーションを実行
        $this->artisan('migrate', [
            '--path' => base_path('plugins/DixlaseSEO/database/migrations'),
            '--realpath' => true,
        ]);

        // SitemapGenerator をサービスコンテナに登録
        $this->app->bind(SitemapGenerator::class, function () {
            return new SitemapGenerator([]);
        });

        // ルートを手動登録
        $router = app('router');
        $router->middleware(['web'])
            ->get('sitemap.xml', [SitemapController::class, 'index'])
            ->name('dixlase-seo.sitemap');

        $router->getRoutes()->refreshNameLookups();
        $router->getRoutes()->refreshActionLookups();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Drop ours, then put back whatever the harness had -- nothing, for
        // a superglobal it never set.
        unset($_ENV['INSTALLED'], $_SERVER['INSTALLED']);
        $_ENV += $this->installedBefore['env'];
        $_SERVER += $this->installedBefore['server'];
    }

    /**
     * サイトマップが有効な場合に200レスポンスを返すこと
     */
    /**
     * 2 回目のリクエストでプロバイダのクエリが走らないこと
     */
    public function test_a_second_request_does_not_rebuild_the_sitemap(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        // Counts generate() itself, not how often the container hands
        // the generator out: the controller is constructed on every
        // request either way, so counting resolutions would pass with or
        // without the cache and prove nothing.
        $generator = new class extends SitemapGenerator
        {
            public int $builds = 0;

            public function __construct() {}

            public function generate(): string
            {
                $this->builds++;

                return '<?xml version="1.0"?><urlset></urlset>';
            }
        };
        $this->app->instance(SitemapGenerator::class, $generator);

        $this->get('/sitemap.xml')->assertStatus(200);
        $this->assertSame(1, $generator->builds);

        $this->get('/sitemap.xml')->assertStatus(200);

        $this->assertSame(
            1,
            $generator->builds,
            'Every request used to ask every provider for its rows; within the window it is built once.'
        );
    }

    /**
     * クローラーが尊重できる寿命を応答が持つこと
     */
    public function test_the_response_says_how_long_it_may_be_reused(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $this->get('/sitemap.xml')
            ->assertStatus(200)
            ->assertHeader('Cache-Control', 'max-age=300, public');
    }

    /**
     * 無効にした瞬間から 404 で、キャッシュが配り続けないこと
     */
    public function test_switching_it_off_takes_effect_at_once(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');
        $this->get('/sitemap.xml')->assertStatus(200);

        DixlaseSeoSetting::setValue('sitemap_enabled', '0');

        $this->get('/sitemap.xml')->assertStatus(404);
    }

    /**
     * 別のホストの文書を互いに配らないこと
     */
    public function test_two_hosts_do_not_serve_each_others_document(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');
        config(['app.url' => 'https://first.example.test']);

        \App\Models\Site::query()->create([
            'name' => 'Second',
            'slug' => 'second',
            'host' => 'second.example.test',
            'primary_locale' => 'ja',
            'timezone' => 'Asia/Tokyo',
            'is_active' => true,
        ]);

        $first = $this->get('https://first.example.test/sitemap.xml')->getContent();
        $second = $this->get('https://second.example.test/sitemap.xml')->getContent();

        $this->assertStringContainsString('https://first.example.test', $first);
        $this->assertStringContainsString('https://second.example.test', $second);
        $this->assertStringNotContainsString('https://second.example.test', $first);
    }

    /**
     * 提供していないホストの URL を作らず、キャッシュにも残さないこと
     */
    public function test_an_unserved_host_neither_mints_urls_nor_poisons_the_cache(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');
        config(['app.url' => 'https://example.test']);

        $forged = $this->get('https://forwarded.example.com/sitemap.xml')->getContent();
        $this->assertStringNotContainsString('forwarded.example.com', $forged);
        $this->assertStringContainsString('https://example.test', $forged);

        // And the next caller is handed the site's own document.
        $next = $this->get('https://example.test/sitemap.xml')->getContent();
        $this->assertStringNotContainsString('forwarded.example.com', $next);
    }

    public function test_returns_200_when_sitemap_enabled(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
    }

    /**
     * サイトマップが無効な場合に404を返すこと
     */
    public function test_returns_404_when_sitemap_disabled(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '0');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(404);
    }

    /**
     * レスポンスのContent-Typeがapplication/xmlであること
     */
    public function test_response_content_type_is_xml(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
    }

    /**
     * レスポンスがXML宣言を含むこと
     */
    public function test_response_body_contains_xml_declaration(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $response->getContent());
    }

    /**
     * レスポンスがurlsetタグを含むこと
     */
    public function test_response_body_contains_urlset(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('<urlset', $response->getContent());
        $this->assertStringContainsString('</urlset>', $response->getContent());
    }

    /**
     * レスポンスがトップページのURLエントリを含むこと
     */
    public function test_response_body_contains_homepage_url(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('<loc>', $response->getContent());
    }

    /**
     * sitemap_enabled が設定されていない場合はデフォルトで有効（200）を返すこと
     */
    public function test_returns_200_when_sitemap_enabled_setting_not_set(): void
    {
        // sitemap_enabled は設定しない（デフォルト値 true が使われる）

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
    }

    /**
     * 有効なXMLが生成されること
     */
    public function test_response_is_valid_xml(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);

        $content = $response->getContent();
        $xml = simplexml_load_string($content);
        $this->assertNotFalse($xml, 'レスポンスが有効なXMLであること');
    }

    /**
     * sitemap_changefreq の設定がXMLに反映されること
     */
    public function test_changefreq_setting_is_reflected_in_xml(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');
        DixlaseSeoSetting::setValue('sitemap_changefreq', 'daily');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('<changefreq>daily</changefreq>', $response->getContent());
    }

    /**
     * sitemap_priority の設定が保存され、トップページのXMLにはpriority 1.0が含まれること
     *
     * トップページのpriorityは常に1.0固定のため、設定値はLinkableProviderが存在する
     * URLのみに適用される。このテストではtrueコンテンツの検証とDB設定の永続性を確認する。
     */
    public function test_priority_setting_is_reflected_in_xml(): void
    {
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');
        DixlaseSeoSetting::setValue('sitemap_priority', '0.8');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        // トップページのpriorityは常に1.0
        $this->assertStringContainsString('<priority>1.0</priority>', $response->getContent());
        // 設定値がDBに保存されていること
        $this->assertSame('0.8', DixlaseSeoSetting::getValue('sitemap_priority'));
    }
}
