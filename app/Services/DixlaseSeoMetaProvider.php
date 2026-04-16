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

use App\Contracts\PluginIntegration\SeoMetaProviderInterface;
use App\DTO\PluginIntegration\SeoMetaDTO;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoMeta;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;

/**
 * SeoMetaProviderInterface の DixlaseSEO による実装
 *
 * dls_plg_dixlase_seo_meta テーブルを読み書きして
 * プラグイン生成コンテンツ単位のSEOメタ情報を管理します。
 */
class DixlaseSeoMetaProvider implements SeoMetaProviderInterface
{
    /**
     * プラグイン別SEO有効化設定のキー形式
     *
     * 例: integration.dixlase-pages.enabled
     */
    private const ENABLED_SETTING_KEY_FORMAT = 'integration.%s.enabled';

    /**
     * 指定プラグインの指定エンティティのメタ情報を取得
     */
    public function getMeta(string $pluginSlug, string $entityId): ?SeoMetaDTO
    {
        $record = DixlaseSeoMeta::query()
            ->where('plugin_slug', $pluginSlug)
            ->where('entity_id', $entityId)
            ->first();

        if (! $record) {
            return null;
        }

        return new SeoMetaDTO(
            description: $record->description,
            ogpMediaId: $record->ogp_media_id,
        );
    }

    /**
     * メタ情報を保存（upsert）
     *
     * 全フィールドが null の DTO が渡された場合はレコードを削除します。
     */
    public function saveMeta(string $pluginSlug, string $entityId, SeoMetaDTO $meta): void
    {
        if ($meta->isEmpty()) {
            $this->deleteMeta($pluginSlug, $entityId);

            return;
        }

        DixlaseSeoMeta::updateOrCreate(
            [
                'plugin_slug' => $pluginSlug,
                'entity_id' => $entityId,
            ],
            [
                'description' => $meta->description,
                'ogp_media_id' => $meta->ogpMediaId,
            ],
        );
    }

    /**
     * 単一のメタ情報を削除
     */
    public function deleteMeta(string $pluginSlug, string $entityId): void
    {
        DixlaseSeoMeta::query()
            ->where('plugin_slug', $pluginSlug)
            ->where('entity_id', $entityId)
            ->delete();
    }

    /**
     * 指定プラグインの全メタ情報を一括削除
     */
    public function purgeByPlugin(string $pluginSlug): int
    {
        return DixlaseSeoMeta::query()
            ->where('plugin_slug', $pluginSlug)
            ->delete();
    }

    /**
     * 指定プラグインのSEOメタ機能が有効化されているかを確認
     *
     * SEOプラグインの設定に integration.{plugin_slug}.enabled が保存され、
     * 明示的に '0' / false の場合のみ無効と判定します。未設定時はデフォルト有効。
     */
    public function isEnabledForPlugin(string $pluginSlug): bool
    {
        $key = sprintf(self::ENABLED_SETTING_KEY_FORMAT, $pluginSlug);
        $value = DixlaseSeoSetting::getValue($key, '1');

        return $value !== '0' && $value !== false;
    }
}
