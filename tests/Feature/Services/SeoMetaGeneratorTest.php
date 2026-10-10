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

use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Services\SeoContext;
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


        // This suite does not boot the plugin's service provider, which is
        // what registers SeoContext as a singleton. Without an explicit
        // binding the container hands out a fresh object on every resolve, so
        // nothing the helper or a test puts in the context would be visible
        // to the generator. Production is unaffected — the provider binds it.
        app()->instance(SeoContext::class, new SeoContext());

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
     * twitter:image タグが og:image と同じ画像URLで生成されること
     * (X が og:image にフォールバックする挙動に依存せず明示出力する)
     */
    public function test_generate_includes_twitter_image_matching_og_image(): void
    {
        DixlaseSeoSetting::setValue('default_ogp_image', 'https://example.com/ogp.jpg');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta name="twitter:image" content="https://example.com/ogp.jpg">', $output);
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

    /**
     * og:title uses the default social title when one is set, instead of
     * repeating the application name that og:site_name already carries.
     */
    public function test_generate_uses_default_title_for_og_title_when_set(): void
    {
        config(['app.name' => 'Dixlase']);
        DixlaseSeoSetting::setValue('default_title', 'A CMS you can actually read');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString(
            '<meta property="og:title" content="A CMS you can actually read">',
            $output
        );
    }

    /**
     * og:title falls back to the application name when the social title is
     * empty, so installs that never touch the new field keep today's output.
     */
    public function test_generate_falls_back_to_app_name_for_og_title_when_default_title_empty(): void
    {
        config(['app.name' => 'Fallback Site']);
        DixlaseSeoSetting::setValue('default_title', '');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta property="og:title" content="Fallback Site">', $output);
    }

    /**
     * twitter:title is emitted explicitly and matches og:title, rather than
     * being left to X's og:title fallback.
     */
    public function test_generate_includes_twitter_title_matching_og_title(): void
    {
        config(['app.name' => 'Dixlase']);
        DixlaseSeoSetting::setValue('default_title', 'Share card headline');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta property="og:title" content="Share card headline">', $output);
        $this->assertStringContainsString('<meta name="twitter:title" content="Share card headline">', $output);
    }

    /**
     * og:title and og:site_name are no longer the same string once a social
     * title is set — the regression this release exists to fix.
     */
    public function test_generate_og_title_differs_from_site_name_when_default_title_set(): void
    {
        config(['app.name' => 'Dixlase']);
        DixlaseSeoSetting::setValue('default_title', 'Distinct headline');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/');

        $this->assertStringContainsString('<meta property="og:site_name" content="Dixlase">', $output);
        $this->assertStringContainsString('<meta property="og:title" content="Distinct headline">', $output);
    }

    /**
     * Build a media repository that resolves only the given id => path pairs
     * and returns null for anything else, so a candidate whose media row is
     * missing falls through to the next step of the chain.
     *
     * @param  array<int, string>  $paths
     */
    private function mediaRepositoryFor(array $paths): MediaRepositoryInterface
    {
        $repository = $this->createMock(MediaRepositoryInterface::class);
        $repository->method('find')->willReturnCallback(
            function (int $id) use ($paths): ?Media {
                if (! isset($paths[$id])) {
                    return null;
                }

                $media = new Media;
                $media->path = $paths[$id];

                return $media;
            }
        );

        return $repository;
    }

    /**
     * The image the editor picked for the entity wins over every other
     * source, and twitter:image matches it.
     */
    public function test_generate_uses_entity_ogp_media_for_image(): void
    {
        DixlaseSeoSetting::setValue('default_ogp_image', 'https://example.com/site-wide.jpg');

        app(SeoContext::class)->setOgpMediaId(11);

        $generator = new SeoMetaGenerator(null, $this->mediaRepositoryFor([11 => 'entity.jpg']));
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString('<meta property="og:image" content="'.asset('storage/media/entity.jpg').'">', $output);
        $this->assertStringContainsString('<meta name="twitter:image" content="'.asset('storage/media/entity.jpg').'">', $output);
        $this->assertStringNotContainsString('site-wide.jpg', $output);
    }

    /**
     * With no image on the entity, the caller-supplied fallback is used —
     * the step a blog post's featured image occupies.
     */
    public function test_generate_uses_fallback_ogp_media_when_entity_has_none(): void
    {
        DixlaseSeoSetting::setValue('default_ogp_image', 'https://example.com/site-wide.jpg');

        app(SeoContext::class)->setFallbackOgpMediaId(22);

        $generator = new SeoMetaGenerator(null, $this->mediaRepositoryFor([22 => 'featured.jpg']));
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString('<meta property="og:image" content="'.asset('storage/media/featured.jpg').'">', $output);
        $this->assertStringNotContainsString('site-wide.jpg', $output);
    }

    /**
     * The entity's image still wins when a fallback is also present.
     */
    public function test_generate_prefers_entity_ogp_media_over_fallback(): void
    {
        $context = app(SeoContext::class);
        $context->setOgpMediaId(11);
        $context->setFallbackOgpMediaId(22);

        $generator = new SeoMetaGenerator(null, $this->mediaRepositoryFor([
            11 => 'entity.jpg',
            22 => 'featured.jpg',
        ]));
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString(asset('storage/media/entity.jpg'), $output);
        $this->assertStringNotContainsString('featured.jpg', $output);
    }

    /**
     * With neither an entity image nor a fallback, the site-wide setting
     * applies — the behaviour every page had before this change.
     */
    public function test_generate_falls_back_to_site_wide_ogp_image(): void
    {
        DixlaseSeoSetting::setValue('default_ogp_image', 'https://example.com/site-wide.jpg');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString('<meta property="og:image" content="https://example.com/site-wide.jpg">', $output);
    }

    /**
     * A candidate whose media row no longer exists does not produce an empty
     * og:image — the chain continues to the next source.
     */
    public function test_generate_skips_unresolvable_media_and_continues_the_chain(): void
    {
        DixlaseSeoSetting::setValue('default_ogp_image', 'https://example.com/site-wide.jpg');

        app(SeoContext::class)->setOgpMediaId(999);

        // The repository resolves nothing, so id 999 yields no URL.
        $generator = new SeoMetaGenerator(null, $this->mediaRepositoryFor([]));
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString('<meta property="og:image" content="https://example.com/site-wide.jpg">', $output);
    }

    /**
     * With no image anywhere, the bundled default keeps the tag non-empty.
     */
    public function test_generate_falls_back_to_bundled_image_when_nothing_is_set(): void
    {
        DixlaseSeoSetting::setValue('default_ogp_image', '');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString('assets/images/default-ogp.png', $output);
    }

    /**
     * The page's own title wins over the site-wide social title, for both
     * og:title and twitter:title.
     */
    public function test_generate_uses_entity_title_for_og_title(): void
    {
        config(['app.name' => 'Dixlase']);
        DixlaseSeoSetting::setValue('default_title', 'Site-wide social title');

        app(SeoContext::class)->setTitle('This page only');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString('<meta property="og:title" content="This page only">', $output);
        $this->assertStringContainsString('<meta name="twitter:title" content="This page only">', $output);
        $this->assertStringNotContainsString('Site-wide social title', $output);
    }

    /**
     * Without a page title the site-wide chain still applies.
     */
    public function test_generate_falls_back_to_site_wide_title_without_entity_title(): void
    {
        DixlaseSeoSetting::setValue('default_title', 'Site-wide social title');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString('<meta property="og:title" content="Site-wide social title">', $output);
    }

    /**
     * The page's own OGP type wins over the ogp_type setting.
     */
    public function test_generate_uses_entity_og_type(): void
    {
        DixlaseSeoSetting::setValue('ogp_type', 'website');

        app(SeoContext::class)->setOgType('article');

        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString('<meta property="og:type" content="article">', $output);
    }

    /**
     * Without a page type the setting applies.
     */
    public function test_generate_falls_back_to_site_wide_og_type(): void
    {
        $generator = new SeoMetaGenerator;
        $output = $generator->generate('https://example.com/page');

        $this->assertStringContainsString('<meta property="og:type" content="website">', $output);
    }
}
