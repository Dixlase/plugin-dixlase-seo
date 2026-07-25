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
    'heading' => 'Sitemap',
    'description' => 'Configure XML sitemap and robots.txt for search engine crawlers.',

    // Section headings
    'section_sitemap' => 'Sitemap',
    'section_robots' => 'robots.txt',

    // Sitemap
    'sitemap_enabled' => 'XML Sitemap',
    'sitemap_enabled_help' => 'When enabled, an XML sitemap is generated at /sitemap.xml.',
    'sitemap_changefreq' => 'Change Frequency',
    'sitemap_changefreq_help' => 'Hint for how frequently the content changes.',
    'sitemap_priority' => 'Priority',
    'sitemap_priority_help' => 'Priority of pages in the sitemap (0.0 to 1.0).',

    // Change frequency options
    'changefreq_always' => 'Always',
    'changefreq_hourly' => 'Hourly',
    'changefreq_daily' => 'Daily',
    'changefreq_weekly' => 'Weekly',
    'changefreq_monthly' => 'Monthly',
    'changefreq_yearly' => 'Yearly',
    'changefreq_never' => 'Never',

    // robots.txt
    'robots_txt_mode' => 'robots.txt Management Mode',
    'robots_txt_mode_help' => 'Auto mode generates a standard robots.txt including the sitemap URL.',
    'robots_txt_mode_auto' => 'Auto-generate',
    'robots_txt_mode_auto_desc' => 'Automatically generates a standard robots.txt',
    'robots_txt_mode_custom' => 'Custom',
    'robots_txt_mode_custom_desc' => 'Freely edit the content',
    'robots_txt_content' => 'robots.txt Content',
    'robots_txt_content_help' => 'Customize the content output to robots.txt.',

    // Flash messages
    'updated' => 'Sitemap & robots.txt settings have been updated.',
];
