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

namespace Plugins\DixlaseSEO\Tests\Unit\Http\Middleware;

use App\Contracts\Cookie\ConsentStateProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use Plugins\DixlaseSEO\App\Http\Middleware\InjectSeoMetaTags;
use Plugins\DixlaseSEO\App\Models\DixlaseSeoSetting;
use Tests\TestCase;

/**
 * Soft-dependency consent gate on the Google Analytics tag.
 *
 * The GA tag is emitted in the deferred Stringable shared by
 * InjectSeoMetaTags. These tests pin the four states of the gate:
 * no consent system, banner off, consent denied, consent granted.
 */
class InjectSeoMetaTagsConsentGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The head-meta Stringable resolves the description through
        // dls_seo_localized_setting(); ensure the helper file is loaded even
        // in a pure unit context where the plugin provider's boot() (which
        // require_once's it) has not run for this request.
        require_once base_path('plugins/DixlaseSEO/app/Helpers/DixlaseSeoHelpers.php');

        config(['dixlase_seo' => require base_path('plugins/DixlaseSEO/config/dixlase_seo.php')]);

        $this->artisan('migrate', [
            '--path' => base_path('plugins/DixlaseSEO/database/migrations'),
            '--realpath' => true,
        ]);

        DixlaseSeoSetting::setValue('google_analytics_id', 'G-TEST');
    }

    public function test_emits_ga_tag_when_no_consent_system_is_installed(): void
    {
        // Ensure no provider is bound (a co-installed consent plugin would
        // otherwise register one) so this exercises the legacy fallback.
        unset($this->app[ConsentStateProviderInterface::class]);

        $html = $this->renderHeadMeta();

        $this->assertStringContainsString('googletagmanager.com/gtag/js?id=G-TEST', $html);
    }

    public function test_emits_ga_tag_when_consent_banner_is_switched_off(): void
    {
        // Provider bound but not collecting consent (banner off) → not gating.
        $this->bindConsentProvider(analyticsGranted: false, bannerEnabled: false);

        $html = $this->renderHeadMeta();

        $this->assertStringContainsString('googletagmanager.com/gtag/js?id=G-TEST', $html);
    }

    public function test_suppresses_ga_tag_when_analytics_consent_is_denied(): void
    {
        $this->bindConsentProvider(analyticsGranted: false, bannerEnabled: true);

        $html = $this->renderHeadMeta();

        $this->assertStringNotContainsString('googletagmanager.com', $html);
    }

    public function test_emits_ga_tag_when_analytics_consent_is_granted(): void
    {
        $this->bindConsentProvider(analyticsGranted: true, bannerEnabled: true);

        $html = $this->renderHeadMeta();

        $this->assertStringContainsString('googletagmanager.com/gtag/js?id=G-TEST', $html);
    }

    public function test_emits_ga_tag_when_consent_gating_is_disabled_by_setting(): void
    {
        // Operator opted out of gating → emit even though consent is denied.
        DixlaseSeoSetting::setValue('respect_cookie_consent', '0');
        $this->bindConsentProvider(analyticsGranted: false, bannerEnabled: true);

        $html = $this->renderHeadMeta();

        $this->assertStringContainsString('googletagmanager.com/gtag/js?id=G-TEST', $html);
    }

    /**
     * Bind a stand-in consent provider. The anonymous class adds the optional
     * isBannerEnabled() probe the gate duck-types for, without coupling the
     * test to DixlaseCookie.
     */
    private function bindConsentProvider(bool $analyticsGranted, bool $bannerEnabled): void
    {
        $provider = new class($analyticsGranted, $bannerEnabled) implements ConsentStateProviderInterface
        {
            public function __construct(private bool $granted, private bool $banner) {}

            public function has(string $category): bool
            {
                return $this->granted;
            }

            public function snapshot(): array
            {
                return [];
            }

            public function version(): int
            {
                return 1;
            }

            public function isBannerEnabled(): bool
            {
                return $this->banner;
            }
        };

        $this->app->instance(ConsentStateProviderInterface::class, $provider);
    }

    /**
     * Run the middleware and render the deferred head-meta Stringable.
     */
    private function renderHeadMeta(): string
    {
        $middleware = app(InjectSeoMetaTags::class);
        $middleware->handle(Request::create('/', 'GET'), fn ($request) => new Response(''));

        return (string) View::shared('seoHeadMeta');
    }
}
