{{--
This file is part of Dixlase SEO.

Copyright (C) 2026 exc-D inc. and Dixlase contributors
https://exc-d.com

Dixlase SEO is dual-licensed. You may use this file under either:

  (a) the GNU General Public License version 3 or later, as published
      by the Free Software Foundation; or

  (b) a commercial license agreement obtained from exc-D inc.

Unless you have entered into a commercial license agreement, this
file is governed by the GPL terms below.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.
--}}

@extends('layouts.admin')

@section('content')
<form id="dixlase-seo-external-form" method="POST" action="{{ route('dixlase-seo::admin.seo.external.update') }}"
    x-data="{ respectConsent: '{{ old('respect_cookie_consent', ((bool) ($settings['respect_cookie_consent'] ?? true)) ? '1' : '0') }}' }">
    @csrf
    @method('PATCH')

    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fab fa-google mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/external.section_google') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            {{-- 1. Measurement ID --}}
            <div>
                <x-form-label for="google_analytics_id" :text="__('dixlase-seo::admin/dixlase-seo/external.google_analytics_id')" class="mb-1" />
                <x-form-text
                    name="google_analytics_id"
                    :value="old('google_analytics_id', $settings['google_analytics_id'] ?? '')"
                    :placeholder="__('dixlase-seo::admin/dixlase-seo/external.google_analytics_id_placeholder')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/external.google_analytics_id_help')" />
            </div>

            {{-- 2. Search Console verification code --}}
            <div>
                <x-form-label for="google_site_verification" :text="__('dixlase-seo::admin/dixlase-seo/external.google_site_verification')" class="mb-1" />
                <x-form-text
                    name="google_site_verification"
                    :value="old('google_site_verification', $settings['google_site_verification'] ?? '')"
                    :placeholder="__('dixlase-seo::admin/dixlase-seo/external.google_site_verification_placeholder')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/external.google_site_verification_help')" />
            </div>

            {{-- 3. Respect-consent toggle --}}
            <div>
                <x-form-toggle
                    name="respect_cookie_consent"
                    xModel="respectConsent"
                    :label="__('dixlase-seo::admin/dixlase-seo/external.respect_cookie_consent')"
                    :checked="old('respect_cookie_consent', $settings['respect_cookie_consent'] ?? true)"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/external.respect_cookie_consent_help')" />
            </div>

            {{-- 4. Cookie-consent status (bottom). Detection is generic: any
                 plugin binding the core consent contract is recognised. The
                 wording also reflects the respect-consent toggle above. --}}
            @if($consentStatus === 'active')
                {{-- Banner active: the wording reacts live to the respect-consent
                     toggle above (Alpine), no save/reload needed. --}}
                <div x-show="respectConsent === '1'" x-cloak class="rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-900 dark:border-green-700/60 dark:bg-green-900/20 dark:text-green-200">
                    <p class="flex items-start gap-2 font-semibold">
                        <i class="fas fa-shield-halved mt-0.5"></i>
                        <span>{{ __('dixlase-seo::admin/dixlase-seo/external.consent_active_title') }}</span>
                    </p>
                    <p class="mt-2">{{ __('dixlase-seo::admin/dixlase-seo/external.consent_active_body') }}</p>
                </div>
                <div x-show="respectConsent !== '1'" x-cloak role="alert" class="rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-700/60 dark:bg-amber-900/20 dark:text-amber-200">
                    <p class="flex items-start gap-2 font-semibold">
                        <i class="fas fa-triangle-exclamation mt-0.5"></i>
                        <span>{{ __('dixlase-seo::admin/dixlase-seo/external.consent_active_off_title') }}</span>
                    </p>
                    <p class="mt-2">{{ __('dixlase-seo::admin/dixlase-seo/external.consent_active_off_body') }}</p>
                </div>
            @else
                <div role="alert" class="rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-700/60 dark:bg-amber-900/20 dark:text-amber-200">
                    <p class="flex items-start gap-2 font-semibold">
                        <i class="fas fa-triangle-exclamation mt-0.5"></i>
                        <span>{{ __('dixlase-seo::admin/dixlase-seo/external.consent_warning_title') }}</span>
                    </p>
                    <p class="mt-2">{{ __('dixlase-seo::admin/dixlase-seo/external.consent_warning_legal') }}</p>
                    <p class="mt-2">
                        {{ $consentStatus === 'banner_off'
                            ? __('dixlase-seo::admin/dixlase-seo/external.consent_warning_banner_off')
                            : __('dixlase-seo::admin/dixlase-seo/external.consent_warning_not_installed') }}
                    </p>
                </div>
            @endif
        </div>
    </section>
</form>
@endsection

@section('save')
    <x-admin.save-button form="dixlase-seo-external-form" />
@endsection
