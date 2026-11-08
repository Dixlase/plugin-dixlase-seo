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

namespace Plugins\DixlaseSEO\App\Support;

use App\Models\Site;
use Illuminate\Http\Request;
use Throwable;

/**
 * The URL to publish as this page's own address.
 *
 * canonical, og:url and the JSON-LD url all say "this page lives here".
 * Taking that from the request's Host means the page says whatever the
 * Host of the request happened to be -- and nothing validates that Host:
 * TrustHosts is off, deliberately, as in Laravel's defaults. A request
 * arriving through a reverse proxy on another domain would be answered
 * with a page pointing at that domain, which matters wherever such a
 * response can reach someone other than whoever sent it.
 *
 * So the request's host is used only when this install actually serves
 * it, and APP_URL's host is used otherwise. That is the same rule core
 * applies to the passkey relying party, for the same reason.
 *
 * The path is always the request's: it is this page's address that is
 * being stated, not the home page's.
 */
class CanonicalUrl
{
    /**
     * The address to publish for the page this request asked for.
     */
    public static function forRequest(Request $request): string
    {
        $appUrl = trim((string) config('app.url'));
        $appHost = strtolower((string) parse_url($appUrl, PHP_URL_HOST));

        if ($appHost === '') {
            // Nothing to fall back to; the request is all there is.
            return $request->url();
        }

        if (in_array(strtolower($request->getHost()), self::servedHosts($appHost), true)) {
            return $request->url();
        }

        return self::origin($appUrl).$request->getPathInfo();
    }

    /**
     * The hosts this install serves: APP_URL's, and every active site's.
     *
     * @return list<string>
     */
    private static function servedHosts(string $appHost): array
    {
        $hosts = [$appHost];

        try {
            $siteHosts = Site::query()
                ->where('is_active', true)
                ->whereNotNull('host')
                ->pluck('host')
                ->all();
        } catch (Throwable) {
            // No sites table yet (installation), or no database at all.
            // APP_URL alone is then the whole answer.
            $siteHosts = [];
        }

        foreach ($siteHosts as $host) {
            if (is_string($host) && trim($host) !== '') {
                $hosts[] = strtolower(trim($host));
            }
        }

        return array_values(array_unique($hosts));
    }

    /**
     * Scheme, host and port of APP_URL, with no trailing slash.
     */
    private static function origin(string $appUrl): string
    {
        $parts = parse_url($appUrl);

        if (! is_array($parts) || ! isset($parts['host'])) {
            return rtrim($appUrl, '/');
        }

        $scheme = $parts['scheme'] ?? 'https';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return $scheme.'://'.$parts['host'].$port;
    }
}
