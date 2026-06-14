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
     * Generate meta tags HTML for the front page
     */
    public function generate(string $url): string
    {
        $settings = $this->getSettings();
        $lines = [];

        // Basic meta tags. The default description is resolved per request
        // locale via DixlaseMultilingual (falls back to the primary-locale
        // value, then to Core's site_description, when no translation or
        // multilingual plugin is present).
        $description = (string) (dls_seo_localized_setting('default_description') ?? '');
        if (! $description && $this->baseSettingRepository) {
            $description = (string) $this->baseSettingRepository->get('site_description', '');
        }
        if ($description) {
            $lines[] = '<meta name="description" content="'.e($description).'">';
        }

        // canonical URL
        $lines[] = '<link rel="canonical" href="'.e($url).'">';

        // OGP tags
        $lines[] = '<meta property="og:type" content="'.e($settings['ogp_type'] ?? 'website').'">';
        $lines[] = '<meta property="og:url" content="'.e($url).'">';
        $lines[] = '<meta property="og:title" content="'.e($this->buildTitle()).'">';

        if ($description) {
            $lines[] = '<meta property="og:description" content="'.e($description).'">';
        }

        $ogpImageValue = $settings['default_ogp_image'] ?? '';
        $ogpImageUrl = $this->resolveMediaUrl($ogpImageValue) ?: url('assets/images/default-ogp.png');
        $lines[] = '<meta property="og:image" content="'.e($ogpImageUrl).'">';

        $lines[] = '<meta property="og:site_name" content="'.e(config('app.name', '')).'">';

        // Twitter Card tags
        $twitterCardType = $settings['twitter_card_type'] ?? 'summary_large_image';
        $lines[] = '<meta name="twitter:card" content="'.e($twitterCardType).'">';

        $twitterSite = $settings['twitter_site'] ?? '';
        if ($twitterSite) {
            $lines[] = '<meta name="twitter:site" content="@'.e($twitterSite).'">';
        }

        // Google Search Console verification
        $googleVerification = $settings['google_site_verification'] ?? '';
        if ($googleVerification) {
            $lines[] = '<meta name="google-site-verification" content="'.e($googleVerification).'">';
        }

        return implode("\n    ", $lines);
    }

    /**
     * Build the site title
     */
    private function buildTitle(): string
    {
        return config('app.name', '');
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
