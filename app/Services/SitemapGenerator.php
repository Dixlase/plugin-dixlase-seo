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

use App\Contracts\PluginIntegration\LinkableProviderInterface;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * XML sitemap generation service
 */
class SitemapGenerator
{
    /**
     * @param  array<LinkableProviderInterface>  $linkableProviders
     */
    public function __construct(
        private readonly array $linkableProviders = [],
    ) {}

    /**
     * Generate XML sitemap
     */
    public function generate(): string
    {
        $settings = DixlaseSeoSetting::getMany(config('dixlase_seo', []));
        $changefreq = $settings['sitemap_changefreq'] ?? 'weekly';
        $priority = $settings['sitemap_priority'] ?? '0.5';

        $urls = $this->collectUrls();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        // Top page
        $xml .= $this->buildUrlEntry(url('/'), $changefreq, '1.0');

        // URLs collected from LinkableProvider
        foreach ($urls as $urlData) {
            $xml .= $this->buildUrlEntry(
                $urlData['url'],
                $changefreq,
                $priority,
                $urlData['lastmod'] ?? null
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Collect URLs from LinkableProvider
     *
     * - Convert relative paths to absolute URLs
     * - Remove duplicates when the same URL is provided by multiple providers
     *
     * @return array<int, array{url: string, lastmod: string|null}>
     */
    private function collectUrls(): array
    {
        $seen = [];
        $urls = [];

        foreach ($this->linkableProviders as $provider) {
            if (! $provider->isAvailable()) {
                continue;
            }

            $items = $provider->getAvailableItems(1000);
            foreach ($items as $item) {
                $absoluteUrl = $this->toAbsoluteUrl($item->url);
                if ($absoluteUrl === '' || isset($seen[$absoluteUrl])) {
                    continue;
                }
                $seen[$absoluteUrl] = true;
                $urls[] = [
                    'url' => $absoluteUrl,
                    'lastmod' => $item->meta['updated_at'] ?? null,
                ];
            }
        }

        return $urls;
    }

    /**
     * Convert relative path to absolute URL
     */
    private function toAbsoluteUrl(string $url): string
    {
        if ($url === '') {
            return '';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return url($url);
    }

    /**
     * Build XML for URL entry
     */
    private function buildUrlEntry(
        string $url,
        string $changefreq,
        string $priority,
        ?string $lastmod = null,
    ): string {
        $entry = "  <url>\n";
        $entry .= '    <loc>'.htmlspecialchars($url, ENT_XML1, 'UTF-8')."</loc>\n";

        if ($lastmod) {
            $entry .= '    <lastmod>'.htmlspecialchars($lastmod, ENT_XML1, 'UTF-8')."</lastmod>\n";
        }

        $entry .= '    <changefreq>'.htmlspecialchars($changefreq, ENT_XML1, 'UTF-8')."</changefreq>\n";
        $entry .= '    <priority>'.htmlspecialchars($priority, ENT_XML1, 'UTF-8')."</priority>\n";
        $entry .= "  </url>\n";

        return $entry;
    }
}
