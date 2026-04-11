<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc.
 * https://exc-d.com
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
 * メタタグ・OGPタグのHTML生成サービス
 */
class SeoMetaGenerator
{
    /**
     * 設定値のキャッシュ
     *
     * @var array<string, mixed>|null
     */
    private ?array $settings = null;

    /**
     * 設定値を取得する（遅延読み込み）
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
     * フロントページ用のメタタグHTMLを生成する
     */
    public function generate(string $url): string
    {
        $settings = $this->getSettings();
        $lines = [];

        // 基本メタタグ
        $description = $settings['default_description'] ?? '';
        if ($description) {
            $lines[] = '<meta name="description" content="'.e($description).'">';
        }

        // canonical URL
        $lines[] = '<link rel="canonical" href="'.e($url).'">';

        // OGPタグ
        $lines[] = '<meta property="og:type" content="'.e($settings['ogp_type'] ?? 'website').'">';
        $lines[] = '<meta property="og:url" content="'.e($url).'">';
        $lines[] = '<meta property="og:title" content="'.e($this->buildTitle()).'">';

        if ($description) {
            $lines[] = '<meta property="og:description" content="'.e($description).'">';
        }

        $ogpImage = $settings['default_ogp_image'] ?? '';
        $ogpImageUrl = $ogpImage
            ? $this->resolveImageUrl($ogpImage)
            : url('assets/images/default-ogp.png');
        $lines[] = '<meta property="og:image" content="'.e($ogpImageUrl).'">';

        $lines[] = '<meta property="og:site_name" content="'.e(config('app.name', '')).'">';

        // Twitter Cardタグ
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
     * サイトタイトルを組み立てる
     */
    private function buildTitle(): string
    {
        return config('app.name', '');
    }

    /**
     * 画像パスを絶対URLに変換する
     */
    private function resolveImageUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url($path);
    }
}
