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

{{--
  Per-content SEO meta information input component

  Used in the edit screen for plugin-generated content (static pages, legal pages,
  blog posts, etc.). Field names on submit are nested as `seo_meta[description]` and
  `seo_meta[ogp_media_id]` (assumes the calling controller receives them together
  and passes to SeoMetaProviderInterface::saveMeta()).

  Props:
    - description (?string): Existing meta description
    - ogpMediaId (?int): Existing OGP image media ID
    - ogpMedia (?Media): Media model for preview display
    - fieldPrefix (string, default 'seo_meta'): Prefix for form field names
    - sectionTitle (?string): Section heading text (if not specified, uses default translation key)
--}}

@props([
    'description' => null,
    'ogpMediaId' => null,
    'ogpMedia' => null,
    'fieldPrefix' => 'seo_meta',
    'sectionTitle' => null,
])

<div class="space-y-4">
    <h3 class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        <i class="fas fa-search mr-1"></i>{{ $sectionTitle ?? __('dixlase-seo::components/meta-fields.section_title') }}
    </h3>

    <div>
        <x-form-label
            for="{{ $fieldPrefix }}_description"
            :text="__('dixlase-seo::components/meta-fields.description')"
            class="mb-1"
        />
        <x-form-textarea
            :id="$fieldPrefix.'_description'"
            :name="$fieldPrefix.'[description]'"
            :value="old($fieldPrefix.'.description', $description ?? '')"
            rows="3"
            maxlength="200"
        />
        <x-form-help-text :text="__('dixlase-seo::components/meta-fields.description_help')" />
    </div>

    <div>
        <x-form-label
            for="{{ $fieldPrefix }}_ogp_media_id"
            :text="__('dixlase-seo::components/meta-fields.ogp_media')"
            class="mb-1"
        />
        <x-media.picker
            :id="$fieldPrefix.'_ogp_media_id'"
            :name="$fieldPrefix.'[ogp_media_id]'"
            :value="old($fieldPrefix.'.ogp_media_id', $ogpMediaId ?? '')"
            :media="$ogpMedia"
            aspectRatio="ogp"
        />
        <x-form-help-text :text="__('dixlase-seo::components/meta-fields.ogp_media_help')" />
    </div>
</div>
