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

return [
    'heading' => 'Plugin Integrations',
    'description' => 'Toggle SEO meta features (meta description, OGP image) per plugin that supports them.',

    // Empty state
    'no_plugins_title' => 'No SEO-meta capable plugins found',
    'no_plugins_description' => 'No plugin declares "capabilities": ["seo-meta"] in plugin.json. When such plugins are installed and enabled, they will appear here automatically.',

    // Flash messages
    'updated' => 'Plugin integration settings have been updated.',
];
