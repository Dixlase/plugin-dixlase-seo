<?php

/**
 * This file is part of DixlaseSEO.
 *
 * Copyright (C) 2026
 * https://example.com
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
            $lines[] = '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>';
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
            $logoUrl = str_starts_with($orgLogo, 'http') ? $orgLogo : url($orgLogo);
            $schema['logo'] = $logoUrl;
        }

        return $schema;
    }
}
