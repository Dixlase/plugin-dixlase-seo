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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイグレーション実行
     *
     * プラグイン生成コンテンツ（固定ページ、リーガルページ、ブログ記事など）の
     * SEOメタ情報を格納するテーブル。
     * (plugin_slug, entity_id) のペアで一意に識別される。
     */
    public function up(): void
    {
        Schema::create('dls_plg_dixlase_seo_meta', function (Blueprint $table) {
            $table->id();
            $table->string('plugin_slug', 100);
            $table->string('entity_id', 100);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('ogp_media_id')->nullable();
            $table->timestamps();

            $table->unique(['plugin_slug', 'entity_id']);
            $table->index('plugin_slug');
        });
    }

    /**
     * ロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('dls_plg_dixlase_seo_meta');
    }
};
