<?php

/**
 * This file is part of Dixlase SEO.
 *
 * Copyright (C) 2026 exc-D inc. and Dixlase contributors
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
    'heading' => 'Base Settings',
    'description' => 'Configure site-wide meta tags, OGP (social media share appearance), and organization info (JSON-LD).',

    // Section headings
    'section_general' => 'General Settings',
    'section_ogp' => 'OGP / Social Media',
    'section_organization' => 'Organization Info (JSON-LD)',

    // General settings
    'title_separator' => 'Title Separator',
    'title_separator_help' => 'Character displayed between page title and site name. Example: Page Name | Site Name',
    'default_description' => 'Default Meta Description',
    'default_description_help' => 'Default description used for pages without an individual description set.',

    // OGP settings
    'default_ogp_image' => 'Default OGP Image',
    'default_ogp_image_help' => 'Default image displayed when shared on social media. Recommended: 1200x630px',
    'twitter_card_type' => 'X Card Type',
    'twitter_card_type_help' => 'Card display format when shared on X (formerly Twitter). Based on the Twitter Cards specification.',
    'twitter_card_summary' => 'Summary (small card)',
    'twitter_card_summary_large' => 'Summary Large Image (large card)',
    'twitter_site' => 'X Account',
    'twitter_site_help' => 'X (formerly Twitter) account name associated with the site (without @).',

    // Organization info
    'organization_name' => 'Organization Name',
    'organization_name_help' => 'Organization name displayed in search results.',
    'organization_logo' => 'Organization Logo',
    'organization_logo_help' => 'Organization logo for search results. Recommended: square, at least 112x112px.',
    'organization_url' => 'Organization URL',
    'organization_url_help' => 'Official website URL of the organization.',

    // Flash messages
    'updated' => 'Meta tags & OGP settings have been updated.',
];
