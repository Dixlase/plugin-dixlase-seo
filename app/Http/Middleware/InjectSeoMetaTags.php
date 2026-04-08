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

namespace Plugins\DixlaseSEO\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Services\JsonLdGenerator;
use Plugins\DixlaseSEO\App\Services\SeoMetaGenerator;
use Symfony\Component\HttpFoundation\Response;

/**
 * フロントページにSEOメタタグを注入するミドルウェア
 */
class InjectSeoMetaTags
{
    public function __construct(
        private readonly SeoMetaGenerator $metaGenerator,
        private readonly JsonLdGenerator $jsonLdGenerator,
    ) {}

    /**
     * リクエストを処理する
     */
    public function handle(Request $request, Closure $next): Response
    {
        $url = $request->url();

        // メタタグとJSON-LDを生成
        $metaHtml = $this->metaGenerator->generate($url);
        $jsonLdHtml = $this->jsonLdGenerator->generate($url);

        // Google Analytics script
        $gaId = DixlaseSeoSetting::getValue('google_analytics_id', config('dixlase_seo.google_analytics_id', ''));
        $gaHtml = '';
        if ($gaId) {
            $escapedId = e($gaId);
            $gaHtml = <<<GA
            <script async src="https://www.googletagmanager.com/gtag/js?id={$escapedId}"></script>
            <script>
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
        View::share('seoHeadMeta', $headMeta);

        return $next($request);
    }
}
