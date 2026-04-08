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

namespace Plugins\DixlaseSEO\Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Services\JsonLdGenerator;
use Tests\TestCase;

/**
 * JsonLdGenerator サービスのフィーチャーテスト
 */
class JsonLdGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // プラグイン設定を手動ロード
        config(['dixlase_seo' => require base_path('plugins/DixlaseSEO/config/dixlase_seo.php')]);

        // プラグインのマイグレーションを実行
        $this->artisan('migrate', [
            '--path' => base_path('plugins/DixlaseSEO/database/migrations'),
            '--realpath' => true,
        ]);
    }

    /**
     * アプリ名が設定されている場合にWebSiteスキーマが生成されること
     */
    public function test_generate_includes_website_schema_when_app_name_set(): void
    {
        config(['app.name' => 'テストサイト']);

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('application/ld+json', $output);
        $this->assertStringContainsString('"@type": "WebSite"', $output);
        $this->assertStringContainsString('"name": "テストサイト"', $output);
        $this->assertStringContainsString('"url": "https://example.com/"', $output);
    }

    /**
     * アプリ名が空の場合にWebSiteスキーマが生成されないこと
     */
    public function test_generate_omits_website_schema_when_app_name_empty(): void
    {
        config(['app.name' => '']);

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertStringNotContainsString('"@type": "WebSite"', $output);
    }

    /**
     * 組織名が設定されている場合にOrganizationスキーマが生成されること
     */
    public function test_generate_includes_organization_schema_when_org_name_set(): void
    {
        config(['app.name' => 'Test Site']);
        DixlaseSeoSetting::setValue('organization_name', '株式会社テスト');

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('"@type": "Organization"', $output);
        $this->assertStringContainsString('"name": "株式会社テスト"', $output);
    }

    /**
     * 組織名が空の場合にOrganizationスキーマが生成されないこと
     */
    public function test_generate_omits_organization_schema_when_org_name_empty(): void
    {
        config(['app.name' => 'Test Site']);
        DixlaseSeoSetting::setValue('organization_name', '');

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertStringNotContainsString('"@type": "Organization"', $output);
    }

    /**
     * 組織URLが設定されている場合にOrganizationスキーマにurlが含まれること
     */
    public function test_organization_schema_includes_url_when_set(): void
    {
        DixlaseSeoSetting::setValue('organization_name', 'Test Corp');
        DixlaseSeoSetting::setValue('organization_url', 'https://testcorp.example.com');

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('"url": "https://testcorp.example.com"', $output);
    }

    /**
     * 組織URLが空の場合にOrganizationスキーマにurlが含まれないこと
     */
    public function test_organization_schema_omits_url_when_empty(): void
    {
        DixlaseSeoSetting::setValue('organization_name', 'Test Corp');
        DixlaseSeoSetting::setValue('organization_url', '');

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        // Organization スキーマの url フィールドが含まれないこと
        // (WebSite スキーマには url があるので、"url" という文字列自体は存在しうる)
        $decoded = $this->extractOrganizationSchema($output);
        $this->assertArrayNotHasKey('url', $decoded);
    }

    /**
     * 組織ロゴが絶対URLで設定されている場合にOrganizationスキーマにlogoが含まれること
     */
    public function test_organization_schema_includes_logo_when_set_with_absolute_url(): void
    {
        DixlaseSeoSetting::setValue('organization_name', 'Test Corp');
        DixlaseSeoSetting::setValue('organization_logo', 'https://example.com/logo.png');

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('"logo": "https://example.com/logo.png"', $output);
    }

    /**
     * 組織ロゴが相対パスの場合に絶対URLに変換されること
     */
    public function test_organization_logo_resolves_relative_path_to_absolute_url(): void
    {
        DixlaseSeoSetting::setValue('organization_name', 'Test Corp');
        DixlaseSeoSetting::setValue('organization_logo', '/images/logo.png');

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        // 相対パスがそのままでないこと（絶対URLに変換されること）
        $this->assertStringNotContainsString('"logo": "/images/logo.png"', $output);
        $this->assertStringContainsString('"logo":', $output);
    }

    /**
     * 組織ロゴが空の場合にOrganizationスキーマにlogoが含まれないこと
     */
    public function test_organization_schema_omits_logo_when_empty(): void
    {
        DixlaseSeoSetting::setValue('organization_name', 'Test Corp');
        DixlaseSeoSetting::setValue('organization_logo', '');

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $decoded = $this->extractOrganizationSchema($output);
        $this->assertArrayNotHasKey('logo', $decoded);
    }

    /**
     * アプリ名も組織名も空の場合に空文字列が返されること
     */
    public function test_generate_returns_empty_string_when_no_data(): void
    {
        config(['app.name' => '']);
        DixlaseSeoSetting::setValue('organization_name', '');

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertSame('', $output);
    }

    /**
     * WebSiteスキーマの@contextがschema.orgであること
     */
    public function test_website_schema_has_correct_context(): void
    {
        config(['app.name' => 'Test Site']);

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('"@context": "https://schema.org"', $output);
    }

    /**
     * 生成された出力がscriptタグで囲まれていること
     */
    public function test_generate_wraps_output_in_script_tags(): void
    {
        config(['app.name' => 'Test Site']);

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<script type="application/ld+json">', $output);
        $this->assertStringContainsString('</script>', $output);
    }

    /**
     * 組織名とアプリ名の両方がある場合に2つのスキーマが生成されること
     */
    public function test_generate_produces_two_schemas_when_both_set(): void
    {
        config(['app.name' => 'Test Site']);
        DixlaseSeoSetting::setValue('organization_name', 'Test Corp');

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('"@type": "WebSite"', $output);
        $this->assertStringContainsString('"@type": "Organization"', $output);
    }

    /**
     * JSON-LDの出力がデコード可能な正しいJSONであること
     */
    public function test_generate_produces_valid_json_in_script_tags(): void
    {
        config(['app.name' => 'Test Site']);

        $generator = new JsonLdGenerator();
        $output = $generator->generate('https://example.com/');

        // scriptタグからJSONを抽出してデコード試行
        preg_match('/<script type="application\/ld\+json">\s*(.*?)\s*<\/script>/s', $output, $matches);
        $this->assertNotEmpty($matches[1], 'script タグ内にJSONが見つかること');

        $decoded = json_decode($matches[1], true);
        $this->assertNotNull($decoded, 'JSONが正しくデコードできること');
    }

    /**
     * Organizationスキーマを抽出するヘルパーメソッド
     *
     * @return array<string, mixed>
     */
    private function extractOrganizationSchema(string $output): array
    {
        preg_match_all('/<script type="application\/ld\+json">\s*(.*?)\s*<\/script>/s', $output, $matches);

        foreach ($matches[1] as $jsonStr) {
            $decoded = json_decode($jsonStr, true);
            if (is_array($decoded) && ($decoded['@type'] ?? '') === 'Organization') {
                return $decoded;
            }
        }

        return [];
    }
}
