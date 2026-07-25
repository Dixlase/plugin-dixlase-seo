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
use Plugins\DixlaseSEO\App\Http\Controllers\RobotsTxtController;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Tests\TestCase;

/**
 * RobotsTxtController のフィーチャーテスト
 */
class RobotsTxtControllerTest extends TestCase
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

        // ルートを手動登録
        $router = app('router');
        $router->middleware(['web'])
            ->get('robots.txt', [RobotsTxtController::class, 'index'])
            ->name('dixlase-seo.robots');

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
     * robots.txt に200レスポンスを返すこと
     */
    public function test_returns_200_response(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
    }

    /**
     * レスポンスのContent-Typeがtext/plainであること
     */
    public function test_response_content_type_is_plain_text(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));
    }

    /**
     * autoモードではUser-agentとAllow行が含まれること
     */
    public function test_auto_mode_includes_user_agent_and_allow(): void
    {
        DixlaseSeoSetting::setValue('robots_txt_mode', 'auto');

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Allow: /', $content);
    }

    /**
     * autoモードでサイトマップが有効な場合にSitemap行が含まれること
     */
    public function test_auto_mode_includes_sitemap_when_enabled(): void
    {
        DixlaseSeoSetting::setValue('robots_txt_mode', 'auto');
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertStringContainsString('Sitemap:', $response->getContent());
        $this->assertStringContainsString('sitemap.xml', $response->getContent());
    }

    /**
     * autoモードでサイトマップが無効な場合にSitemap行が含まれないこと
     */
    public function test_auto_mode_omits_sitemap_when_disabled(): void
    {
        DixlaseSeoSetting::setValue('robots_txt_mode', 'auto');
        DixlaseSeoSetting::setValue('sitemap_enabled', '0');

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertStringNotContainsString('Sitemap:', $response->getContent());
    }

    /**
     * customモードではカスタムrobots.txtの内容が返されること
     */
    public function test_custom_mode_returns_custom_content(): void
    {
        $customContent = "User-agent: *\nDisallow: /admin\nDisallow: /private\n";
        DixlaseSeoSetting::setValue('robots_txt_mode', 'custom');
        DixlaseSeoSetting::setValue('robots_txt', $customContent);

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertSame($customContent, $response->getContent());
    }

    /**
     * customモードでrobots_txtが空の場合に空レスポンスが返されること
     */
    public function test_custom_mode_returns_empty_when_custom_content_is_empty(): void
    {
        DixlaseSeoSetting::setValue('robots_txt_mode', 'custom');
        DixlaseSeoSetting::setValue('robots_txt', '');

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertSame('', $response->getContent());
    }

    /**
     * モードが未設定の場合はautoモードで動作すること
     */
    public function test_defaults_to_auto_mode_when_not_set(): void
    {
        // robots_txt_mode を設定しない（デフォルト値 'auto' が使われる）

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertStringContainsString('User-agent: *', $response->getContent());
        $this->assertStringContainsString('Allow: /', $response->getContent());
    }

    /**
     * autoモードのSitmapURLが正しいURL形式であること
     */
    public function test_auto_mode_sitemap_url_is_absolute(): void
    {
        DixlaseSeoSetting::setValue('robots_txt_mode', 'auto');
        DixlaseSeoSetting::setValue('sitemap_enabled', '1');

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $content = $response->getContent();
        // Sitemap: で始まる行を抽出
        preg_match('/Sitemap:\s*(.+)/', $content, $matches);
        $this->assertNotEmpty($matches[1], 'Sitemap URLが存在すること');
        $this->assertStringStartsWith('http', trim($matches[1]), 'Sitemap URLが絶対URLであること');
    }

    /**
     * customモードでHTMLタグがそのまま返されること（エスケープされないこと）
     */
    public function test_custom_mode_returns_content_as_is(): void
    {
        $customContent = "# コメント行\nUser-agent: Googlebot\nAllow: /\n";
        DixlaseSeoSetting::setValue('robots_txt_mode', 'custom');
        DixlaseSeoSetting::setValue('robots_txt', $customContent);

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertSame($customContent, $response->getContent());
    }

    /**
     * autoモードの出力が改行で終わること
     */
    public function test_auto_mode_output_ends_with_newline(): void
    {
        DixlaseSeoSetting::setValue('robots_txt_mode', 'auto');

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertStringEndsWith("\n", $response->getContent());
    }
}
