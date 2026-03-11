<?php

/**
 * This file is part of DixlaseSEO.
 *
 * Copyright (C) 2026
 * https://example.com
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

        // 全ビューでアクセス可能な変数として共有
        $headMeta = trim($metaHtml . "\n    " . $jsonLdHtml);
        View::share('seoHeadMeta', $headMeta);

        return $next($request);
    }
}
