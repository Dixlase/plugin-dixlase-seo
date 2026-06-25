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
    'heading' => 'External Services',
    'description' => 'Integrate with external services such as Google Analytics and Google Search Console.',

    // Section headings
    'section_google' => 'Google Services',

    // Google Analytics
    'google_analytics_id' => 'Google Analytics Measurement ID',
    'google_analytics_id_help' => 'Enter your GA4 measurement ID to automatically embed the tracking code on your site.',
    'google_analytics_id_placeholder' => 'G-XXXXXXXXXX',

    // Cookie-consent status shown next to the GA field. Detection is generic:
    // any plugin binding the core consent contract is recognised.
    'consent_active_title' => 'Cookie consent banner detected',
    'consent_active_body' => 'A cookie consent banner is active. Google Analytics runs only after the visitor grants the "Analytics" consent category.',
    'consent_active_off_title' => 'Consent banner detected, but consent is not enforced',
    'consent_active_off_body' => 'A consent banner is active, but the toggle above is off, so Google Analytics runs for every visitor regardless of their choice. Turn it on to run Analytics only for visitors who grant the "Analytics" category.',
    'consent_warning_title' => 'No cookie consent banner is active',
    'consent_warning_legal' => 'Google Analytics sets analytics cookies, which require the visitor\'s prior consent in the EU, the UK, Brazil and many other jurisdictions. Running Analytics without a consent banner there is a legal violation.',
    'consent_warning_not_installed' => 'We recommend installing a cookie consent plugin (such as Dixlase Cookie) and showing its banner. With the banner active and "Respect visitor cookie consent" above kept on, Analytics runs only for visitors who grant the "Analytics" category. Until then, the tag fires for every visitor.',
    'consent_warning_banner_off' => 'A cookie consent plugin is installed but its banner is switched off. Turn the banner on and keep "Respect visitor cookie consent" above on, so Analytics runs only for visitors who grant the "Analytics" category. While the banner is off, the tag fires for every visitor.',

    // Respect-consent toggle
    'respect_cookie_consent' => 'Respect visitor cookie consent for Analytics',
    'respect_cookie_consent_help' => 'When on, Analytics does not run until the visitor grants the "Analytics" category (consenting to "Necessary" only is not enough). When off, it runs regardless of consent.',

    // Search Console
    'google_site_verification' => 'Google Search Console Verification Code',
    'google_site_verification_help' => 'Enter the content attribute value from the "HTML tag" verification method in Search Console.',
    'google_site_verification_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',

    // Flash messages
    'updated' => 'External services settings have been updated.',
];
