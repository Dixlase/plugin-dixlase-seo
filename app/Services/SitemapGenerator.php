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
        $localeContext = $this->localeContext();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        if ($localeContext !== null) {
            // Required namespace for the <xhtml:link rel="alternate"> hreflang
            // annotations emitted per URL when multiple locales are enabled.
            $xml .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml"';
        }
        $xml .= ">\n";

        // Top page
        $xml .= $this->buildUrlEntry(url('/'), $changefreq, '1.0', null, $localeContext);

        // URLs collected from LinkableProvider
        foreach ($urls as $urlData) {
            $xml .= $this->buildUrlEntry(
                $urlData['url'],
                $changefreq,
                $priority,
                $urlData['lastmod'] ?? null,
                $localeContext
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Resolve the multilingual context for hreflang annotations, or null
     * when locale URL routing is off or fewer than two locales are enabled
     * (in which case a single non-localised <loc> is emitted, as before).
     *
     * Soft dependency: the enabled-locale list is owned by DixlaseMultilingual.
     * When that plugin is absent the resolver is unbound and we fall back to
     * single-locale output.
     *
     * @return array{locales: list<string>, default: string}|null
     */
    private function localeContext(): ?array
    {
        if (! config('dixlase_multilingual.locale_url_routing_enabled')) {
            return null;
        }

        $resolverClass = 'Plugins\\DixlaseMultilingual\\App\\Services\\EnabledLocaleResolver';
        if (! app()->bound($resolverClass)) {
            return null;
        }

        try {
            $locales = array_values(array_filter(
                app($resolverClass)->getEnabledLocales(),
                'is_string'
            ));
            $default = \App\Helpers\LocaleHelper::getSiteDefaultLocale();
        } catch (\Throwable) {
            return null;
        }

        if (count($locales) < 2 || $default === '') {
            return null;
        }

        return ['locales' => $locales, 'default' => $default];
    }

    /**
     * Build the locale-specific variant of a bare (default-locale) URL.
     *
     * The default locale keeps the bare URL; every other locale gets a
     * `/{locale}` path prefix, matching the multilingual plugin's
     * Route::prefix('{locale}') front routing.
     */
    private function localizedUrl(string $bareUrl, string $locale, string $defaultLocale): string
    {
        if ($locale === $defaultLocale) {
            return $bareUrl;
        }

        $parts = parse_url($bareUrl);
        if ($parts === false || ! isset($parts['host'])) {
            return $bareUrl;
        }

        $path = $parts['path'] ?? '/';
        $prefixedPath = '/'.$locale.($path === '/' ? '' : $path);

        $scheme = isset($parts['scheme']) ? $parts['scheme'].'://' : '//';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $query = isset($parts['query']) ? '?'.$parts['query'] : '';

        return $scheme.$parts['host'].$port.$prefixedPath.$query;
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
        ?array $localeContext = null,
    ): string {
        $entry = "  <url>\n";
        $entry .= '    <loc>'.htmlspecialchars($url, ENT_XML1, 'UTF-8')."</loc>\n";

        // hreflang alternates: one per enabled locale (default locale keeps
        // the bare URL, others get a /{locale} prefix), plus x-default which
        // points at the bare default-locale URL. The bare <loc> above is the
        // canonical default version, so the prefixed default URL is never
        // listed separately (it would be duplicate content).
        if ($localeContext !== null) {
            foreach ($localeContext['locales'] as $locale) {
                $href = $this->localizedUrl($url, $locale, $localeContext['default']);
                $entry .= '    <xhtml:link rel="alternate" hreflang="'
                    .htmlspecialchars($locale, ENT_XML1, 'UTF-8').'" href="'
                    .htmlspecialchars($href, ENT_XML1, 'UTF-8')."\"/>\n";
            }
            $entry .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'
                .htmlspecialchars($url, ENT_XML1, 'UTF-8')."\"/>\n";
        }

        if ($lastmod) {
            $entry .= '    <lastmod>'.htmlspecialchars($lastmod, ENT_XML1, 'UTF-8')."</lastmod>\n";
        }

        $entry .= '    <changefreq>'.htmlspecialchars($changefreq, ENT_XML1, 'UTF-8')."</changefreq>\n";
        $entry .= '    <priority>'.htmlspecialchars($priority, ENT_XML1, 'UTF-8')."</priority>\n";
        $entry .= "  </url>\n";

        return $entry;
    }
}
