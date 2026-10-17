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

use App\Contracts\PluginIntegration\SeoMetaProviderInterface;
use Illuminate\Database\Eloquent\Model;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Multilingual\SeoSettingsProvider;
use Plugins\DixlaseSEO\App\Services\SeoContext;

if (! function_exists('dls_seo_set_page_meta')) {
    /**
     * Hand a content page's own SEO meta to SEO so SeoMetaGenerator emits it
     * in the `<head>` instead of the site-wide defaults.
     *
     * Description: the page's `seo_description` translation for the current
     * locale (stored under the page's own translation morph and edited in
     * the central translation manager alongside title/content), falling
     * back to the primary value stored in the SEO meta table (authored in
     * the page editor). Empty results leave the site-wide default to apply.
     *
     * OGP image: the page's `seo_ogp_media_id` translation for the current
     * locale, when the plugin declares that field, then the `ogp_media_id`
     * the editor picked for this entity, read from the same SEO meta table.
     * Needs no argument — a caller that still passes only `$page` and
     * `$pluginSlug` gets its per-page image.
     *
     * The remaining arguments are optional, so every existing two-argument
     * call keeps working unchanged; pass them by name:
     *
     *     dls_seo_set_page_meta($post, 'dixlase-blog',
     *         title: $post->title,
     *         fallbackOgpMediaId: $post->featured_media_id,
     *         ogType: 'article',
     *     );
     *
     * Called from a front view (e.g. `front/page.blade.php`) before the
     * layout's `<head>` is rendered. No-op and never throws when the SEO
     * plugin or its context is unavailable.
     *
     * @param  Model  $page  The entity being rendered; a model using core's TranslatableTrait
     * @param  string  $pluginSlug  Slug of the plugin that owns the entity
     * @param  string|null  $title  The page's title in the current locale, for `og:title`
     * @param  int|null  $fallbackOgpMediaId  Image to use when the entity has no SEO image of its own
     * @param  string|null  $ogType  OGP type for this page, e.g. `article`
     */
    function dls_seo_set_page_meta(
        Model $page,
        string $pluginSlug,
        ?string $title = null,
        ?int $fallbackOgpMediaId = null,
        ?string $ogType = null,
    ): void {
        try {
            $locale = app()->getLocale();
            $context = app(SeoContext::class);

            $context->setEntity($pluginSlug, (string) $page->getKey());

            // Per-locale translation (no column fallback — primary lives in
            // the SEO meta table, not on the page).
            $description = null;
            if (method_exists($page, 'getTranslation')) {
                $translated = $page->getTranslation('seo_description', $locale, false);
                if (is_string($translated) && $translated !== '') {
                    $description = $translated;
                }
            }

            // Read the entity's stored meta once. This is not gated on the
            // description being empty: the row also carries the OGP image,
            // which is needed whether or not a translation supplied the
            // description.
            $meta = null;
            if (app()->bound(SeoMetaProviderInterface::class)) {
                $meta = app(SeoMetaProviderInterface::class)->getMeta($pluginSlug, (string) $page->getKey());
            }

            // Fall back to the primary-locale value from the SEO meta table.
            if ($description === null && $meta !== null
                && is_string($meta->description) && $meta->description !== '') {
                $description = $meta->description;
            }

            if ($description !== null) {
                $context->setDescription($description);
            }

            if ($meta !== null && $meta->ogpMediaId !== null) {
                $context->setOgpMediaId((int) $meta->ogpMediaId);
            }

            if ($title !== null && $title !== '') {
                $context->setTitle($title);
            }

            if ($fallbackOgpMediaId !== null) {
                $context->setFallbackOgpMediaId($fallbackOgpMediaId);
            }

            if ($ogType !== null && $ogType !== '') {
                $context->setOgType($ogType);
            }

            // The image made for this locale, tried before the stored one.
            // Looked up last, so a page that fails here still has everything
            // else set. The generator falls through when the media item is
            // gone.
            if (method_exists($page, 'getTranslation')) {
                $translatedImage = $page->getTranslation('seo_ogp_media_id', $locale, false);
                if (is_int($translatedImage) || (is_string($translatedImage) && ctype_digit($translatedImage))) {
                    $context->setLocalizedOgpMediaId((int) $translatedImage);
                }
            }
        } catch (\Throwable) {
            // SEO context not bound (plugin disabled / console) — ignore.
        }
    }
}

if (! function_exists('dls_seo_localized_setting')) {
    /**
     * Read a translatable DixlaseSEO site setting in the current locale.
     *
     * Lookup chain:
     *
     *   1. Core's `SingletonTranslationResolver` contract for the
     *      current locale (anchored on
     *      `(translatable_type='dixlase-seo:settings', translatable_id=1)`)
     *   2. Same resolver for the site's default locale
     *   3. The primary value from `plg_dixlase_seo_settings`
     *
     * On a single-language site steps 1 and 2 are skipped because
     * nothing binds that contract, and the helper returns the primary
     * value directly.
     *
     * Intended fields: default_title and default_description. Callers
     * outside this set get the primary value via the same path.
     */
    function dls_seo_localized_setting(string $key): ?string
    {
        try {
            // Step 0: when the current locale equals the provider's
            // primary locale (the site default), skip the resolver
            // entirely — the primary IS the value for that locale. This
            // also avoids the empty-row trap when the operator opens a
            // primary-locale tab in the central translation manager and
            // saves it blank.
            try {
                $primaryLocale = (new SeoSettingsProvider())->getPrimaryLocale();
            } catch (\Throwable) {
                $primaryLocale = null;
            }
            if ($primaryLocale !== null && $primaryLocale === app()->getLocale()) {
                return seo_primary_setting_value($key);
            }

            if (app()->bound(\App\Contracts\Multilingual\SingletonTranslationResolver::class)) {
                /** @var \App\Contracts\Multilingual\SingletonTranslationResolver $resolver */
                $resolver = app(\App\Contracts\Multilingual\SingletonTranslationResolver::class);

                // Treat an empty string the same as "no translation". The
                // central translation manager UI stores every field of a
                // locale row even when the operator leaves them blank, so
                // a brand-new locale tab saves `""` for each field on
                // first save. Without this guard the helper would return
                // `""` and suppress the primary-value fallback.
                $value = $resolver->resolve('dixlase-seo:settings', $key, app()->getLocale());
                if ($value !== null && $value !== '') {
                    return (string) $value;
                }

                try {
                    $siteDefault = \App\Helpers\LocaleHelper::getSiteDefaultLocale();
                } catch (\Throwable) {
                    $siteDefault = null;
                }

                if (is_string($siteDefault) && $siteDefault !== app()->getLocale()) {
                    $value = $resolver->resolve('dixlase-seo:settings', $key, $siteDefault);
                    if ($value !== null && $value !== '') {
                        return (string) $value;
                    }
                }
            }

            return seo_primary_setting_value($key);
        } catch (\Throwable) {
            // Defensive: never let a translation lookup break the page.
            try {
                return seo_primary_setting_value($key);
            } catch (\Throwable) {
                return null;
            }
        }
    }
}

if (! function_exists('seo_primary_setting_value')) {
    /**
     * Read the primary (site-default-locale) value of a SEO setting from
     * the plugin's own key-value store, merging the config default.
     */
    function seo_primary_setting_value(string $key): ?string
    {
        $value = DixlaseSeoSetting::getValue($key, config('dixlase_seo.'.$key));

        return $value === null ? null : (string) $value;
    }
}
