{{--
This file is part of Dixlase SEO.

Copyright (C) 2026 exc-D inc.
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
<form id="dixlase-seo-external-form" method="POST" action="{{ route('dixlase-seo::admin.seo.external.update') }}">
    @csrf
    @method('PATCH')

    <section class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fab fa-google mr-2"></i>{{ __('dixlase-seo::admin/dixlase-seo/external.section_google') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 space-y-6">
            <div>
                <x-form-label for="google_analytics_id" :text="__('dixlase-seo::admin/dixlase-seo/external.google_analytics_id')" class="mb-1" />
                <x-form-text
                    name="google_analytics_id"
                    :value="old('google_analytics_id', $settings['google_analytics_id'] ?? '')"
                    :placeholder="__('dixlase-seo::admin/dixlase-seo/external.google_analytics_id_placeholder')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/external.google_analytics_id_help')" />
            </div>

            <div>
                <x-form-label for="google_site_verification" :text="__('dixlase-seo::admin/dixlase-seo/external.google_site_verification')" class="mb-1" />
                <x-form-text
                    name="google_site_verification"
                    :value="old('google_site_verification', $settings['google_site_verification'] ?? '')"
                    :placeholder="__('dixlase-seo::admin/dixlase-seo/external.google_site_verification_placeholder')"
                />
                <x-form-help-text :text="__('dixlase-seo::admin/dixlase-seo/external.google_site_verification_help')" />
            </div>
        </div>
    </section>
</form>
@endsection

@section('save')
    <x-admin.save-button form="dixlase-seo-external-form" />
@endsection
