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

use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * JSON-LD構造化データの生成サービス（WebSite + Organization）
 */
class JsonLdGenerator
{
    /**
     * 設定値のキャッシュ
     *
     * @var array<string, mixed>|null
     */
    private ?array $settings = null;

    /**
     * 設定値を取得する
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
     * JSON-LDスクリプトタグを生成する
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
     * WebSiteスキーマを構築する
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
     * Organizationスキーマを構築する
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
     * 組織ロゴの値（メディアID・URL・パス）を絶対URLに解決する
     */
    private function resolveLogoUrl(mixed $value): string
    {
        if (empty($value)) {
            return '';
        }

        if (is_numeric($value)) {
            $media = \App\Models\Media::find((int) $value);
            if ($media && $media->path) {
                return asset('storage/media/'.$media->path);
            }

            return '';
        }

        $str = (string) $value;

        return str_starts_with($str, 'http') ? $str : url($str);
    }
}
