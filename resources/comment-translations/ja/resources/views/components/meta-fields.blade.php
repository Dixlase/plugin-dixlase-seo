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

return [
    '{{--
  Per-content SEO meta information input component

  Used in the edit screen for plugin-generated content (static pages, legal pages,
  blog posts, etc.). Field names on submit are nested as `seo_meta[description]` and
  `seo_meta[ogp_media_id]` (assumes the calling controller receives them together
  and passes to SeoMetaProviderInterface::saveMeta()).

  Props:
    - description (?string): Existing meta description
    - ogpMediaId (?int): Existing OGP image media ID
    - ogpMedia (?Media): Media model for preview display
    - fieldPrefix (string, default \'seo_meta\'): Prefix for form field names
    - sectionTitle (?string): Section heading text (if not specified, uses default translation key)
--}}' => '{{--
  コンテンツ単位SEOメタ情報入力コンポーネント

  プラグイン生成コンテンツ（固定ページ、リーガルページ、ブログ記事など）の
  編集画面で使用される。送信時のフィールド名は `seo_meta[description]` と
  `seo_meta[ogp_media_id]` でネストされる（呼び出し側のコントローラーで
  まとめて受け取り SeoMetaProviderInterface::saveMeta() に渡す前提）。

  Props:
    - description (?string): 既存のメタディスクリプション
    - ogpMediaId (?int): 既存のOGP画像メディアID
    - ogpMedia (?Media): プレビュー表示用のMediaモデル
    - fieldPrefix (string, default \'seo_meta\'): フォームフィールド名のプレフィックス
    - sectionTitle (?string): セクション見出しテキスト（指定なしならデフォルト翻訳キー）
--}}',

    // ----- metadata (underscore-prefixed; ignored as translation entries) -----
    '_review_status' => [
        '{{--
  Per-content SEO meta information input component

  Used in the edit screen for plugin-generated content (static pages, legal pages,
  blog posts, etc.). Field names on submit are nested as `seo_meta[description]` and
  `seo_meta[ogp_media_id]` (assumes the calling controller receives them together
  and passes to SeoMetaProviderInterface::saveMeta()).

  Props:
    - description (?string): Existing meta description
    - ogpMediaId (?int): Existing OGP image media ID
    - ogpMedia (?Media): Media model for preview display
    - fieldPrefix (string, default \'seo_meta\'): Prefix for form field names
    - sectionTitle (?string): Section heading text (if not specified, uses default translation key)
--}}' => 'machine',
    ],
];
