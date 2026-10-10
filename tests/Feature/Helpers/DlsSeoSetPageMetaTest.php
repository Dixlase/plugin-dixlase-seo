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

namespace Plugins\DixlaseSEO\Tests\Feature\Helpers;

use App\Contracts\PluginIntegration\SeoMetaProviderInterface;
use App\DTO\PluginIntegration\SeoMetaDTO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Plugins\DixlaseSEO\App\Services\SeoContext;
use Tests\TestCase;

/**
 * Feature tests for the dls_seo_set_page_meta() helper.
 *
 * The helper swallows every Throwable by design (it runs from front views
 * that must not break when SEO is disabled), so these tests assert on what
 * reached SeoContext rather than on the absence of an exception.
 */
class DlsSeoSetPageMetaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['dixlase_seo' => require base_path('plugins/DixlaseSEO/config/dixlase_seo.php')]);
    }

    /**
     * A stand-in content model. It deliberately has no getTranslation(), so
     * the helper takes the SEO meta table path for the description.
     */
    private function stubPage(string $id = '42'): Model
    {
        return new class($id) extends Model
        {
            protected $table = 'dls_seo_stub_pages';

            public function __construct(private string $stubKey = '42')
            {
                parent::__construct();
            }

            public function getKey(): string
            {
                return $this->stubKey;
            }
        };
    }

    /**
     * Bind a provider returning the given meta for any entity.
     */
    private function bindMeta(?SeoMetaDTO $meta): void
    {
        $provider = $this->createMock(SeoMetaProviderInterface::class);
        $provider->method('getMeta')->willReturn($meta);

        app()->instance(SeoMetaProviderInterface::class, $provider);
    }

    /**
     * The two-argument call every existing front view uses keeps working,
     * and now also picks up the entity's OGP image without being asked.
     */
    public function test_two_argument_call_still_sets_description_and_now_the_image(): void
    {
        $this->bindMeta(new SeoMetaDTO(description: 'From the meta table', ogpMediaId: 11));

        dls_seo_set_page_meta($this->stubPage(), 'dixlase-pages');

        $context = app(SeoContext::class);

        $this->assertSame('From the meta table', $context->getDescription());
        $this->assertSame(11, $context->getOgpMediaId());
        $this->assertSame('dixlase-pages', $context->getEntityPluginSlug());
        $this->assertSame('42', $context->getEntityId());

        // Nothing the caller did not pass:
        $this->assertNull($context->getTitle());
        $this->assertNull($context->getFallbackOgpMediaId());
        $this->assertNull($context->getOgType());
    }

    /**
     * The optional arguments land in the context when passed by name.
     */
    public function test_named_arguments_are_recorded(): void
    {
        $this->bindMeta(null);

        dls_seo_set_page_meta(
            $this->stubPage('7'),
            'dixlase-blog',
            title: 'A post title',
            fallbackOgpMediaId: 22,
            ogType: 'article',
        );

        $context = app(SeoContext::class);

        $this->assertSame('A post title', $context->getTitle());
        $this->assertSame(22, $context->getFallbackOgpMediaId());
        $this->assertSame('article', $context->getOgType());
        $this->assertSame('dixlase-blog', $context->getEntityPluginSlug());
        $this->assertSame('7', $context->getEntityId());
    }

    /**
     * An entity with no stored meta leaves the site-wide values to apply,
     * while the entity itself is still recorded.
     */
    public function test_entity_without_stored_meta_leaves_slots_empty(): void
    {
        $this->bindMeta(null);

        dls_seo_set_page_meta($this->stubPage(), 'dixlase-legal');

        $context = app(SeoContext::class);

        $this->assertNull($context->getDescription());
        $this->assertNull($context->getOgpMediaId());
        $this->assertSame('dixlase-legal', $context->getEntityPluginSlug());
    }

    /**
     * An empty title or OGP type is treated as "not supplied", so a caller
     * passing a blank value does not blank out the site-wide default.
     */
    public function test_empty_strings_are_ignored(): void
    {
        $this->bindMeta(null);

        dls_seo_set_page_meta($this->stubPage(), 'dixlase-pages', title: '', ogType: '');

        $context = app(SeoContext::class);

        $this->assertNull($context->getTitle());
        $this->assertNull($context->getOgType());
    }

    /**
     * The image is read even when the description came from a translation,
     * which is the case the previous implementation missed: it only looked
     * at the meta table when the description was still empty.
     */
    public function test_image_is_read_even_when_a_translation_supplied_the_description(): void
    {
        $this->bindMeta(new SeoMetaDTO(description: 'Primary description', ogpMediaId: 33));

        $page = new class extends Model
        {
            protected $table = 'dls_seo_stub_translated_pages';

            public function getKey(): string
            {
                return '99';
            }

            public function getTranslation(string $field, string $locale, bool $fallback = true): ?string
            {
                return $field === 'seo_description' ? 'Translated description' : null;
            }
        };

        dls_seo_set_page_meta($page, 'dixlase-pages');

        $context = app(SeoContext::class);

        $this->assertSame('Translated description', $context->getDescription());
        $this->assertSame(33, $context->getOgpMediaId());
    }
}
