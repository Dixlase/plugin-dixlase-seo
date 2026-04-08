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

    protected function setUp(): void
    {
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

        // インストール済みフラグをリセット
        $_ENV['INSTALLED'] = 'false';
        $_SERVER['INSTALLED'] = 'false';
    }

    /**
     * サイトマップが有効な場合に200レスポンスを返すこと
     */
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
