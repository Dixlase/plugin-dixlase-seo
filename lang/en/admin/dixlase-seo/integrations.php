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
    'heading' => 'Plugin Integrations',
    'description' => 'Toggle SEO meta features (meta description, OGP image) per plugin that supports them.',

    // Plugin list
    'section_plugins' => 'Integrated Plugins',
    'enable_for_plugin' => 'Enable SEO meta for :name',

    // Empty state
    'no_plugins_title' => 'No SEO-meta capable plugins found',
    'no_plugins_description' => 'No plugin declares "capabilities": ["seo-meta"] in plugin.json. When such plugins are installed and enabled, they will appear here automatically.',

    // Orphan cleanup
    'section_cleanup' => 'Maintenance',
    'orphans_title' => 'Orphan Meta Cleanup',
    'orphans_none' => 'No orphan meta records found.',
    'orphans_description' => 'There are :count orphan meta records from plugins that no longer support SEO meta (uninstalled, disabled, or capability no longer declared). Deleting them will not affect the plugins\' actual content.',
    'orphans_by_plugin' => ':slug: :count records',
    'purge_orphans_button' => 'Delete Orphan Meta',
    'purge_orphans_confirm' => 'This will permanently delete :count orphan meta records. This operation cannot be undone. Continue?',

    // Flash messages
    'updated' => 'Plugin integration settings have been updated.',
    'orphans_purged' => 'Deleted :count orphan meta records.',
];
