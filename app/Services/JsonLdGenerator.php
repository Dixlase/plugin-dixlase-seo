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
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * JSON-LD structured data generation service (WebSite + Organization)
 */
class JsonLdGenerator
{
    /**
     * Settings value cache
     *
     * @var array<string, mixed>|null
     */
    private ?array $settings = null;

    public function __construct(
        private readonly ?MediaRepositoryInterface $mediaRepository = null,
    ) {}

    /**
     * Retrieve settings values
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
     * Generate JSON-LD script tag
     */
    public function generate(string $url): string
    {
        $schemas = [];

        $websiteSchema = $this->buildWebSiteSchema($url);
        if ($websiteSchema) {
            $schemas[] = $websiteSchema;
        }

        $organizationSchema = $this->buildOrganizationSchema();
        if ($organizationSchema) {
            $schemas[] = $organizationSchema;
        }

        if (empty($schemas)) {
            return '';
        }

        $lines = [];
        foreach ($schemas as $schema) {
            $json = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $lines[] = '<script type="application/ld+json">'."\n".$json."\n".'</script>';
        }

        return implode("\n    ", $lines);
    }

    /**
     * Build WebSite schema
     *
     * @return array<string, mixed>|null
     */
    private function buildWebSiteSchema(string $url): ?array
    {
        $siteName = config('app.name', '');
        if (! $siteName) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $url,
        ];
    }

    /**
     * Build Organization schema
     *
     * @return array<string, mixed>|null
     */
    private function buildOrganizationSchema(): ?array
    {
        $settings = $this->getSettings();

        $orgName = $settings['organization_name'] ?? '';
        if (! $orgName) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $orgName,
        ];

        $orgUrl = $settings['organization_url'] ?? '';
        if ($orgUrl) {
            $schema['url'] = $orgUrl;
        }

        $orgLogo = $settings['organization_logo'] ?? '';
        if ($orgLogo) {
            $logoUrl = $this->resolveLogoUrl($orgLogo);
            if ($logoUrl) {
                $schema['logo'] = $logoUrl;
            }
        }

        return $schema;
    }

    /**
     * Resolve organization logo value (media ID, URL, or path) to absolute URL
     */
    private function resolveLogoUrl(mixed $value): string
    {
        if (empty($value)) {
            return '';
        }

        if (is_numeric($value)) {
            $repository = $this->mediaRepository ?? app(MediaRepositoryInterface::class);
            $media = $repository->find((int) $value);
            if ($media && $media->path) {
                return asset('storage/media/'.$media->path);
            }

            return '';
        }

        $str = (string) $value;

        return str_starts_with($str, 'http') ? $str : url($str);
    }
}
