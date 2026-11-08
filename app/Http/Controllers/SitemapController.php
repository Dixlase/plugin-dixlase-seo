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

namespace Plugins\DixlaseSEO\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Plugins\DixlaseSEO\App\Services\SitemapGenerator;
use Plugins\DixlaseSEO\App\Support\CanonicalUrl;

/**
 * XML sitemap controller
 */
class SitemapController extends Controller
{
    /**
     * How long a built sitemap is reused.
     *
     * Every request used to ask every provider for its URLs -- up to a
     * thousand rows each -- and rebuild the document. Minutes rather than
     * hours: a page published now should appear without the operator
     * clearing anything, and a crawler polling the file costs one build
     * per window instead of one per request.
     */
    private const CACHE_SECONDS = 300;

    public function __construct(
        private readonly SitemapGenerator $sitemapGenerator,
    ) {}

    /**
     * Output XML sitemap
     */
    public function index(Request $request): Response
    {
        $enabled = DixlaseSeoSetting::getValue('sitemap_enabled', '1');

        // Checked before the cache is read, so switching the sitemap off
        // takes effect at once rather than at the end of the window.
        if ($enabled !== '1' && $enabled !== true) {
            abort(404);
        }

        // The listed URLs are built with url(), which resolves against the
        // request. Pin it to an origin this install serves, so a request
        // carrying some other Host neither mints URLs on that host nor
        // leaves them in the cache for whoever asks next.
        $origin = CanonicalUrl::originForRequest($request);
        URL::forceRootUrl($origin);

        // Keyed on the origin: a multi-site install serves a different
        // document per host, and they must not be handed each other's.
        $xml = Cache::remember(
            'dixlase-seo:sitemap:'.sha1($origin),
            self::CACHE_SECONDS,
            fn (): string => $this->sitemapGenerator->generate(),
        );

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age='.self::CACHE_SECONDS,
        ]);
    }
}
