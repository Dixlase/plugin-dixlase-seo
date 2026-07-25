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

/*
|--------------------------------------------------------------------------
| DixlaseSEO Default Settings
|--------------------------------------------------------------------------
|
| Defines default settings values for the SEO plugin.
| These values are overridden by settings saved in the database.
|
*/

return [
    // Title separator
    'title_separator' => '|',

    // Default meta description
    'default_description' => '',

    // Default OGP image path (media library path)
    'default_ogp_image' => '',

    // OGP type (site-wide)
    'ogp_type' => 'website',

    // Twitter Card type
    'twitter_card_type' => 'summary_large_image',

    // Twitter username (without @)
    'twitter_site' => '',

    // Organization name
    'organization_name' => '',

    // Organization logo URL
    'organization_logo' => '',

    // Organization URL
    'organization_url' => '',

    // robots.txt custom content
    'robots_txt' => '',

    // robots.txt management mode ('auto' or 'custom')
    'robots_txt_mode' => 'auto',

    // Sitemap enabled
    'sitemap_enabled' => true,

    // Sitemap change frequency
    'sitemap_changefreq' => 'weekly',

    // Sitemap priority
    'sitemap_priority' => '0.5',

    // Google Analytics measurement ID (GA4)
    'google_analytics_id' => '',

    // Whether to gate the Google Analytics tag on visitor cookie consent
    // (the "analytics" category) when a consent banner is active. When off,
    // the tag is emitted even if the visitor declined — the operator takes
    // responsibility for compliance.
    'respect_cookie_consent' => true,

    // Google Search Console site verification code
    'google_site_verification' => '',
];
