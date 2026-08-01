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

namespace Plugins\DixlaseSEO\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Services\JsonLdGenerator;
use Plugins\DixlaseSEO\App\Services\SeoMetaGenerator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to inject SEO meta tags into front pages
 */
class InjectSeoMetaTags
{
    public function __construct(
        private readonly SeoMetaGenerator $metaGenerator,
        private readonly JsonLdGenerator $jsonLdGenerator,
    ) {}

    /**
     * Handle the request
     */
    public function handle(Request $request, Closure $next): Response
    {
        $url = $request->url();

        // Share the head content as a deferred Stringable instead of a
        // pre-rendered string. The meta description is locale-aware, but
        // the request locale is only finalised by the multilingual
        // locale-resolver middleware — which, for `/{locale}/` prefixed
        // routes, runs in the route group *after* this web-group
        // middleware's before-phase. Building the string here would
        // therefore capture the default locale. Deferring evaluation to
        // the blade `{!! $seoHeadMeta !!}` render (which happens after all
        // middleware have set the locale) yields the correct translation,
        // and skips the work entirely for non-HTML responses.
        $metaGenerator = $this->metaGenerator;
        $jsonLdGenerator = $this->jsonLdGenerator;

        View::share('seoHeadMeta', new class($metaGenerator, $jsonLdGenerator, $url) implements \Stringable
        {
            public function __construct(
                private readonly SeoMetaGenerator $metaGenerator,
                private readonly JsonLdGenerator $jsonLdGenerator,
                private readonly string $url,
            ) {}

            public function __toString(): string
            {
                $metaHtml = $this->metaGenerator->generate($this->url);
                $jsonLdHtml = $this->jsonLdGenerator->generate($this->url);

                // Google Analytics script — gated by visitor cookie consent
                // when a consent provider (typically DixlaseCookie) is actively
                // collecting consent. When none is installed, or its banner is
                // switched off, we fall through to the historic always-emit
                // behaviour so the plugin stays usable on sites without consent
                // collection.
                $gaId = DixlaseSeoSetting::getValue('google_analytics_id', config('dixlase_seo.google_analytics_id', ''));
                $gaHtml = '';
                if ($gaId && $this->analyticsConsentGranted()) {
                    $escapedId = e($gaId);
                    // The inline gtag bootstrap must carry the request's CSP
                    // nonce, otherwise the site's strict script-src (no
                    // 'unsafe-inline') blocks it and GA never initialises even
                    // though the external loader is allow-listed. csp_nonce_attr()
                    // is the same helper @cspNonce expands to; guard with
                    // function_exists so the plugin still renders standalone
                    // (no CSP layer) where the helper is absent.
                    $nonceAttr = function_exists('csp_nonce_attr') ? csp_nonce_attr() : '';
                    $gaHtml = <<<GA
                    <script async src="https://www.googletagmanager.com/gtag/js?id={$escapedId}"></script>
                    <script {$nonceAttr}>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
                    gtag('config', '{$escapedId}');
                    </script>
                    GA;
                }

                // Combine all head content
                $headMeta = trim($metaHtml . "\n    " . $jsonLdHtml);
                if ($gaHtml) {
                    $headMeta = $gaHtml . "\n    " . $headMeta;
                }

                return $headMeta;
            }

            /**
             * Decide whether the analytics tag may be emitted on this request.
             *
             * Soft dependency on the cross-plugin
             * App\Contracts\Cookie\ConsentStateProviderInterface:
             *   - No provider bound (no consent plugin) → emit (legacy behaviour).
             *   - Provider bound but not actively collecting consent (its banner
             *     is switched off, surfaced via the optional isBannerEnabled()
             *     probe) → emit; the operator is not gating, so do not suppress
             *     forever.
             *   - Provider bound and collecting → defer to has('analytics').
             */
            private function analyticsConsentGranted(): bool
            {
                // Operator opted out of consent gating → always emit.
                if (! (bool) DixlaseSeoSetting::getValue('respect_cookie_consent', config('dixlase_seo.respect_cookie_consent', true))) {
                    return true;
                }

                if (! app()->bound(\App\Contracts\Cookie\ConsentStateProviderInterface::class)) {
                    return true;
                }

                $provider = app(\App\Contracts\Cookie\ConsentStateProviderInterface::class);

                if (method_exists($provider, 'isBannerEnabled') && ! $provider->isBannerEnabled()) {
                    return true;
                }

                return $provider->has(\App\Enums\ConsentCategory::Analytics->value);
            }
        });

        return $next($request);
    }
}
