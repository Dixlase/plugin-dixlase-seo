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

namespace Plugins\DixlaseSEO\Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Services\SeoMetaGenerator;
use Tests\TestCase;

/**
 * SeoMetaGenerator サービスのフィーチャーテスト
 */
class SeoMetaGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private SeoMetaGenerator $generator;

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

        $this->generator = new SeoMetaGenerator;
    }

    /**
     * canonical URLタグが生成されること
     */
    public function test_generate_includes_canonical_url(): void
    {
        $output = $this->generator->generate('https://example.com/page');

        $this->assertStringContainsString('<link rel="canonical" href="https://example.com/page">', $output);
    }

    /**
     * OGP og:url タグが生成されること
     */
    public function test_generate_includes_og_url(): void
    {
        $output = $this->generator->generate('https://example.com/page');

        $this->assertStringContainsString('<meta property="og:url" content="https://example.com/page">', $output);
    }

    /**
     * OGP og:type タグが生成されること
     */
    public function test_generate_includes_og_type(): void
    {
        $output = $this->generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta property="og:type" content="website">', $output);
    }

    /**
     * OGP og:site_name タグがアプリ名で生成されること
     */
    public function test_generate_includes_og_site_name(): void
    {
        config(['app.name' => 'テストサイト']);

        $output = $this->generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta property="og:site_name" content="テストサイト">', $output);
    }

    /**
     * OGP og:title タグがアプリ名で生成されること
     */
    public function test_generate_includes_og_title(): void
    {
        config(['app.name' => 'My Site']);

        $output = $this->generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta property="og:title" content="My Site">', $output);
    }

    /**
     * デフォルト説明文が設定されている場合にメタdescriptionタグが生成されること
     */
    public function test_generate_includes_description_when_set(): void
    {
        DixlaseSeoSetting::setValue('default_description', 'このサイトの説明文です。');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta name="description" content="このサイトの説明文です。">', $output);
        $this->assertStringContainsString('<meta property="og:description" content="このサイトの説明文です。">', $output);
    }

    /**
     * デフォルト説明文が空の場合にメタdescriptionタグが生成されないこと
     */
    public function test_generate_omits_description_when_empty(): void
    {
        DixlaseSeoSetting::setValue('default_description', '');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringNotContainsString('<meta name="description"', $output);
        $this->assertStringNotContainsString('<meta property="og:description"', $output);
    }

    /**
     * Twitter Cardタグがデフォルトで summary_large_image で生成されること
     */
    public function test_generate_includes_twitter_card_default(): void
    {
        $output = $this->generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image">', $output);
    }

    /**
     * Twitter Cardタイプが summary に変更されると反映されること
     */
    public function test_generate_uses_custom_twitter_card_type(): void
    {
        DixlaseSeoSetting::setValue('twitter_card_type', 'summary');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta name="twitter:card" content="summary">', $output);
    }

    /**
     * Twitter サイトユーザー名が設定されている場合に twitter:site タグが生成されること
     */
    public function test_generate_includes_twitter_site_when_set(): void
    {
        DixlaseSeoSetting::setValue('twitter_site', 'mytwitterhandle');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta name="twitter:site" content="@mytwitterhandle">', $output);
    }

    /**
     * Twitter サイトユーザー名が空の場合に twitter:site タグが生成されないこと
     */
    public function test_generate_omits_twitter_site_when_empty(): void
    {
        DixlaseSeoSetting::setValue('twitter_site', '');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringNotContainsString('twitter:site', $output);
    }

    /**
     * OGP画像が設定されている場合に og:image タグが生成されること
     */
    public function test_generate_includes_og_image_when_set(): void
    {
        DixlaseSeoSetting::setValue('default_ogp_image', 'https://example.com/ogp.jpg');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta property="og:image" content="https://example.com/ogp.jpg">', $output);
    }

    /**
     * OGP画像が相対パスの場合に絶対URLに変換されること
     */
    public function test_generate_resolves_relative_ogp_image_to_absolute_url(): void
    {
        DixlaseSeoSetting::setValue('default_ogp_image', '/images/ogp.jpg');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta property="og:image" content="', $output);
        // 相対パスがそのままでないこと（絶対URLに変換されること）
        $this->assertStringNotContainsString('content="/images/ogp.jpg"', $output);
    }

    /**
     * OGP画像が空の場合にデフォルトOGP画像にフォールバックすること
     */
    public function test_generate_falls_back_to_default_ogp_image_when_empty(): void
    {
        DixlaseSeoSetting::setValue('default_ogp_image', '');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('og:image', $output);
        $this->assertStringContainsString('assets/images/default-ogp.png', $output);
    }

    /**
     * HTMLの特殊文字がエスケープされること
     */
    public function test_generate_escapes_special_characters_in_description(): void
    {
        DixlaseSeoSetting::setValue('default_description', 'Test <script>alert("xss")</script>');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringNotContainsString('<script>alert("xss")</script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
    }

    /**
     * URLの特殊文字がエスケープされること
     */
    public function test_generate_escapes_special_characters_in_url(): void
    {
        $url = 'https://example.com/page?a=1&b=2';

        $output = $this->generator->generate($url);

        $this->assertStringContainsString('&amp;', $output);
    }

    /**
     * generate の戻り値が文字列であること
     */
    public function test_generate_returns_string(): void
    {
        $output = $this->generator->generate('https://example.com/');

        $this->assertIsString($output);
    }

    /**
     * generate の出力が空でないこと
     */
    public function test_generate_output_is_not_empty(): void
    {
        $output = $this->generator->generate('https://example.com/');

        $this->assertNotEmpty($output);
    }
}
