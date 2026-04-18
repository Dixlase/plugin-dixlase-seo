{{--
This file is part of Dixlase SEO.

Copyright (C) 2026 exc-D inc.
https://exc-d.com

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
<section class="mb-8">
    @if (empty($seoMetaPluginSlugs))
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-gray-400 dark:text-gray-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                        {{ __('dixlase-seo::admin/dixlase-seo/integrations.no_plugins_title') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('dixlase-seo::admin/dixlase-seo/integrations.no_plugins_description') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <ul class="space-y-2">
                @foreach ($seoMetaPluginSlugs as $slug)
                    <li class="flex items-center">
                        <i class="fas fa-cube text-blue-500 dark:text-blue-400 mr-2"></i>
                        <code class="text-sm text-gray-800 dark:text-gray-200">{{ $slug }}</code>
                    </li>
                @endforeach
            </ul>
            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                {{-- TODO: タスクCで各プラグインごとのON/OFFトグルと孤立メタ削除 danger zone を追加予定 --}}
            </p>
        </div>
    @endif
</section>
@endsection
