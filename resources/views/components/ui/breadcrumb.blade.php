@if ($crumbs !== null)
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            @foreach ($crumbs as $crumb)
                <li @if ($loop->last) aria-current="page" @endif>
                    <div class="flex items-center">
                        @if ($loop->index > 0)
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        @endif
                        @if (isset($crumb['url']))
                            <a href="{{ $crumb['url'] }}" @if(isset($crumb['title'])) title="{{ $crumb['title'] }}" @endif class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">{!! $crumb['text'] !!}</a>
                        @else
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-500">{!! $crumb['text'] !!}</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </nav>
@endif
