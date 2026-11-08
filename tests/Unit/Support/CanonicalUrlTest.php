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

namespace Plugins\DixlaseSEO\Tests\Unit\Support;

use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Plugins\DixlaseSEO\App\Support\CanonicalUrl;
use Tests\TestCase;

/**
 * The address a page publishes as its own.
 *
 * canonical, og:url and the JSON-LD url were taken from the request's
 * Host, and nothing validates that Host (TrustHosts is off by design).
 * The host is now used only when this install serves it; APP_URL's is
 * used otherwise, and the path is always the request's. See #80.
 */
class CanonicalUrlTest extends TestCase
{
    use RefreshDatabase;

    private function request(string $url): Request
    {
        return Request::create($url);
    }

    #[Test]
    public function the_site_s_own_host_is_used_as_it_arrives(): void
    {
        config(['app.url' => 'https://example.test']);

        $this->assertSame(
            'https://example.test/news/2026',
            CanonicalUrl::forRequest($this->request('https://example.test/news/2026'))
        );
    }

    #[Test]
    public function another_host_does_not_become_the_page_s_address(): void
    {
        config(['app.url' => 'https://example.test']);

        $this->assertSame(
            'https://example.test/news/2026',
            CanonicalUrl::forRequest($this->request('https://forwarded.example.com/news/2026')),
            'A Host this install does not serve must not end up in the page.'
        );
    }

    #[Test]
    public function the_path_is_always_the_request_s(): void
    {
        config(['app.url' => 'https://example.test']);

        // Including the locale prefix a multilingual site adds.
        $this->assertSame(
            'https://example.test/ja/about',
            CanonicalUrl::forRequest($this->request('https://forwarded.example.com/ja/about'))
        );
    }

    #[Test]
    public function an_active_site_s_host_counts_as_served(): void
    {
        config(['app.url' => 'https://example.test']);

        Site::query()->create([
            'name' => 'Second',
            'slug' => 'second',
            'host' => 'second.example.test',
            'primary_locale' => 'ja',
            'timezone' => 'Asia/Tokyo',
            'is_active' => true,
        ]);

        $this->assertSame(
            'https://second.example.test/about',
            CanonicalUrl::forRequest($this->request('https://second.example.test/about')),
            'A multi-site install serves more than one host, and each page belongs to its own.'
        );
    }

    #[Test]
    public function an_inactive_site_s_host_does_not(): void
    {
        config(['app.url' => 'https://example.test']);

        Site::query()->create([
            'name' => 'Retired',
            'slug' => 'retired',
            'host' => 'retired.example.test',
            'primary_locale' => 'ja',
            'timezone' => 'Asia/Tokyo',
            'is_active' => false,
        ]);

        $this->assertSame(
            'https://example.test/about',
            CanonicalUrl::forRequest($this->request('https://retired.example.test/about'))
        );
    }

    #[Test]
    public function the_scheme_and_port_come_from_app_url_on_the_fallback(): void
    {
        config(['app.url' => 'http://example.test:8080']);

        $this->assertSame(
            'http://example.test:8080/about',
            CanonicalUrl::forRequest($this->request('https://forwarded.example.com/about'))
        );
    }

    #[Test]
    public function with_no_app_url_the_request_is_all_there_is(): void
    {
        // Nothing to fall back to, so nothing is claimed.
        config(['app.url' => '']);

        $this->assertSame(
            'https://whatever.example.com/about',
            CanonicalUrl::forRequest($this->request('https://whatever.example.com/about'))
        );
    }

    #[Test]
    public function the_middleware_asks_for_the_resolved_url(): void
    {
        $source = (string) file_get_contents(
            base_path('plugins/DixlaseSEO/app/Http/Middleware/InjectSeoMetaTags.php')
        );

        $this->assertStringContainsString('CanonicalUrl::forRequest($request)', $source);
        $this->assertStringNotContainsString('$url = $request->url();', $source);
    }
}
