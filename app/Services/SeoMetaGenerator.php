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

namespace Plugins\DixlaseSEO\App\Services;

use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Repositories\SiteSettingRepositoryInterface;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * Service for generating meta tags and OGP tags HTML
 */
class SeoMetaGenerator
{
    /**
     * Cache of settings values
     *
     * @var array<string, mixed>|null
     */
    private ?array $settings = null;

    public function __construct(
        private readonly ?SiteSettingRepositoryInterface $baseSettingRepository = null,
        private readonly ?MediaRepositoryInterface $mediaRepository = null,
    ) {}

    /**
     * Retrieve settings values (lazy loading)
     *
     * @return array<string, mixed>
     */
    private function getSettings(): array
    {
        if ($this->settings === null) {
            $this->settings = DixlaseSeoSetting::getMany(
                config('dixlase_seo', [])
            );
        }

        return $this->settings;
    }

    /**
     * Resolve the meta description for the current request locale.
     *
     * Precedence:
     *   1. The description a front view resolved for the current content
     *      and handed over via dls_seo_set_page_meta() (its own per-locale
     *      translation, falling back to its primary SEO meta value).
     *   2. The site-wide default description (also locale-aware).
     *
     * Returns '' when neither yields a value; the caller then applies the
     * Core site_description fallback.
     */
    private function resolveDescription(): string
    {
        $explicit = $this->context()?->getDescription();

        if (is_string($explicit) && $explicit !== '') {
            return $explicit;
        }

        return (string) (dls_seo_localized_setting('default_description') ?? '');
    }

    /**
     * Generate meta tags HTML for the front page
     */
    public function generate(string $url): string
    {
        $settings = $this->getSettings();
        $lines = [];

        // Basic meta tags. The description is resolved per request locale:
        // the current entity's own meta description (when a front view
        // declared one) takes precedence, otherwise the site-wide default.
        // Both fall back to the primary-locale value, then to Core's
        // site_description, when no translation/entity is present.
        $description = $this->resolveDescription();
        if (! $description && $this->baseSettingRepository) {
            $description = (string) $this->baseSettingRepository->get('site_description', '');
        }
        if ($description) {
            $lines[] = '<meta name="description" content="'.e($description).'">';
        }

        // canonical URL
        $lines[] = '<link rel="canonical" href="'.e($url).'">';

        // OGP tags
        $lines[] = '<meta property="og:type" content="'.e($this->resolveOgType($settings)).'">';
        $lines[] = '<meta property="og:url" content="'.e($url).'">';
        $title = $this->buildTitle();
        $lines[] = '<meta property="og:title" content="'.e($title).'">';

        if ($description) {
            $lines[] = '<meta property="og:description" content="'.e($description).'">';
        }

        $ogpImageUrl = $this->resolveOgpImageUrl($settings);
        $lines[] = '<meta property="og:image" content="'.e($ogpImageUrl).'">';

        $lines[] = '<meta property="og:site_name" content="'.e(config('app.name', '')).'">';

        // Twitter Card tags
        $twitterCardType = $settings['twitter_card_type'] ?? 'summary_large_image';
        $lines[] = '<meta name="twitter:card" content="'.e($twitterCardType).'">';

        $twitterSite = $settings['twitter_site'] ?? '';
        if ($twitterSite) {
            $lines[] = '<meta name="twitter:site" content="@'.e($twitterSite).'">';
        }

        // Emit twitter:title explicitly for the same reason as twitter:image
        // below: X does fall back to og:title, but relying on that fallback
        // leaves the card at the mercy of how the crawler resolves tags.
        $lines[] = '<meta name="twitter:title" content="'.e($title).'">';

        // Emit twitter:image explicitly (reusing the resolved OGP image) rather
        // than relying on X's og:image fallback, so the card image is
        // deterministic regardless of how the crawler resolves tags.
        $lines[] = '<meta name="twitter:image" content="'.e($ogpImageUrl).'">';

        // Google Search Console verification
        $googleVerification = $settings['google_site_verification'] ?? '';
        if ($googleVerification) {
            $lines[] = '<meta name="google-site-verification" content="'.e($googleVerification).'">';
        }

        return implode("\n    ", $lines);
    }

    /**
     * Resolve the OGP / social title for the current request locale.
     *
     * Precedence:
     *   1. The rendered page's own title, when a front view handed one over
     *      via dls_seo_set_page_meta() — already resolved for the request
     *      locale by the caller.
     *   2. The site-wide default social title, resolved through
     *      dls_seo_localized_setting() so the multilingual overlay for the
     *      request locale wins over the primary-locale value.
     *   3. The application name, which is what every install emitted before
     *      this setting existed, so an empty field changes nothing.
     *
     * Deliberately distinct from og:site_name: the site name answers "which
     * site is this", the title answers "what is this page about", and a share
     * card that repeats one string twice answers neither.
     */
    private function buildTitle(): string
    {
        $entityTitle = (string) ($this->context()?->getTitle() ?? '');

        if ($entityTitle !== '') {
            return $entityTitle;
        }

        $title = (string) (dls_seo_localized_setting('default_title') ?? '');

        if ($title !== '') {
            return $title;
        }

        return config('app.name', '');
    }

    /**
     * Resolve the request-scoped SEO context, or null when it is unavailable
     * (plugin disabled, console command, no container binding).
     */
    private function context(): ?SeoContext
    {
        try {
            return app(SeoContext::class);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Resolve the OGP type for this request.
     *
     * The page's own type (e.g. `article` for a blog post) wins over the
     * site-wide `ogp_type` setting.
     *
     * @param  array<string, mixed>  $settings
     */
    private function resolveOgType(array $settings): string
    {
        $entityType = (string) ($this->context()?->getOgType() ?? '');

        if ($entityType !== '') {
            return $entityType;
        }

        return (string) ($settings['ogp_type'] ?? 'website');
    }

    /**
     * Resolve the share-card image URL, trying each source in turn:
     *
     *   1. The image the editor picked for this entity (`ogp_media_id` in
     *      the SEO meta table).
     *   2. A caller-supplied fallback for entities that have no SEO image
     *      of their own — a blog post's featured image, for instance.
     *   3. The site-wide `default_ogp_image` setting.
     *   4. The image bundled with this plugin, so the tag is never empty.
     *
     * A candidate that does not resolve to a URL (a media row that was
     * deleted, say) falls through to the next one rather than emitting an
     * empty `og:image`.
     *
     * @param  array<string, mixed>  $settings
     */
    private function resolveOgpImageUrl(array $settings): string
    {
        $context = $this->context();

        $candidates = [
            $context?->getOgpMediaId(),
            $context?->getFallbackOgpMediaId(),
            $settings['default_ogp_image'] ?? '',
        ];

        foreach ($candidates as $candidate) {
            $url = $this->resolveMediaUrl($candidate);

            if ($url !== '') {
                return $url;
            }
        }

        return url('assets/images/default-ogp.png');
    }

    /**
     * Convert image path to absolute URL
     */
    private function resolveImageUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url($path);
    }

    /**
     * Resolve media ID, URL, or path to absolute URL
     */
    private function resolveMediaUrl(mixed $value): string
    {
        if (empty($value)) {
            return '';
        }

        // If numeric ID, resolve from Media repository
        if (is_numeric($value)) {
            $repository = $this->mediaRepository ?? app(MediaRepositoryInterface::class);
            $media = $repository->find((int) $value);
            if ($media && $media->path) {
                return asset('storage/media/'.$media->path);
            }

            return '';
        }

        // If string, treat as URL or path
        return $this->resolveImageUrl((string) $value);
    }
}
