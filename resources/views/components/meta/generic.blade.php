@if(config('default.app_demo'))
<meta name="robots" content="noindex, nofollow" />
@endif
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, shrink-to-fit=no">
<link rel="canonical" href="{{ url()->current() }}" />
@if (count($languages['all']) > 1)
    @foreach ($languages['all'] as $language)
        @if (!$language['current'])
<link rel="alternate" href="{{ $language['canonical'] }}" hreflang="{{ $language['localeSlug'] }}" />
        @endif
    @endforeach
@endif