<?php

/**
 * This file is part of DixlaseSEO.
 *
 * Copyright (C) 2026
 * example.com
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
    'heading' => 'SEO Settings',
    'description' => 'Manage site-wide SEO settings. Configure meta tags, OGP, structured data, sitemap, and robots.txt.',

    // Section headings
    'section_general' => 'General Settings',
    'section_ogp' => 'OGP / Social Media',
    'section_organization' => 'Organization Info (JSON-LD)',
    'section_sitemap' => 'Sitemap',
    'section_robots' => 'robots.txt',

    // General settings
    'title_separator' => 'Title Separator',
    'title_separator_help' => 'Character displayed between page title and site name. Example: Page Name | Site Name',
    'default_description' => 'Default Meta Description',
    'default_description_help' => 'Default description used for pages without an individual description set.',

    // OGP settings
    'default_ogp_image' => 'Default OGP Image',
    'default_ogp_image_help' => 'Default image displayed when shared on social media. Recommended: 1200x630px',
    'twitter_card_type' => 'Twitter Card Type',
    'twitter_card_type_help' => 'Card display format when shared on Twitter.',
    'twitter_card_summary' => 'Summary (small card)',
    'twitter_card_summary_large' => 'Summary Large Image (large card)',
    'twitter_site' => 'Twitter Account',
    'twitter_site_help' => 'Twitter account name associated with the site (without @).',

    // Organization info
    'organization_name' => 'Organization Name',
    'organization_name_help' => 'Organization name displayed in search results.',
    'organization_logo' => 'Organization Logo',
    'organization_logo_help' => 'URL of the organization logo for search results. Recommended: square, at least 112x112px.',
    'organization_url' => 'Organization URL',
    'organization_url_help' => 'Official website URL of the organization.',

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

    // External services
    'section_external' => 'External Services',
    'google_analytics_id' => 'Google Analytics Measurement ID',
    'google_analytics_id_help' => 'Enter your GA4 measurement ID to automatically embed the tracking code on your site.',
    'google_analytics_id_placeholder' => 'G-XXXXXXXXXX',
    'google_site_verification' => 'Google Search Console Verification Code',
    'google_site_verification_help' => 'Enter the content attribute value from the "HTML tag" verification method in Search Console.',
    'google_site_verification_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',

    // Flash messages
    'updated' => 'SEO settings have been updated.',
];
