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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename the SEO tables so they stop carrying a double dls_ prefix.
     *
     * The original create migrations hard-coded the dls_ prefix in the
     * table name (Schema::create('dls_plg_dixlase_seo_meta', ...)) while
     * the database connection also adds its dls_ prefix, so existing
     * installs ended up with dls_dls_plg_dixlase_seo_{meta,settings}.
     * The create migrations have been corrected so fresh installs get
     * the convention-following single-prefix names; this migration
     * renames the existing tables to match.
     *
     * Schema::hasTable / Schema::rename both apply the connection
     * prefix, so the table names passed below are WITHOUT the
     * connection prefix.
     */
    public function up(): void
    {
        foreach (['seo_meta', 'seo_settings'] as $name) {
            $oldArg = 'dls_plg_dixlase_'.$name; // resolves to dls_dls_plg_dixlase_<name>
            $newArg = 'plg_dixlase_'.$name;     // resolves to dls_plg_dixlase_<name>
            if (Schema::hasTable($oldArg) && ! Schema::hasTable($newArg)) {
                Schema::rename($oldArg, $newArg);
            }
        }
    }

    public function down(): void
    {
        foreach (['seo_meta', 'seo_settings'] as $name) {
            $oldArg = 'dls_plg_dixlase_'.$name;
            $newArg = 'plg_dixlase_'.$name;
            if (Schema::hasTable($newArg) && ! Schema::hasTable($oldArg)) {
                Schema::rename($newArg, $oldArg);
            }
        }
    }
};
