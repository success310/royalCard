@php
if($settings['overrideTitle']) {
    $pageTitle = $settings['overrideTitle'];
} else {
    $pageTitle = trans('common.add_item') . config('default.page_title_delimiter') . $settings['title'];
}
@endphp

@extends($settings['guard'].'.layouts.default')

@section('page_title', $pageTitle . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
    <div class="w-full" @onclicktab="window.appSetImageUploadHeight()">
        <div class="relative bg-white shadow-md dark:bg-gray-800 sm:rounded-lg m-0 sm:m-4">
            <div
                class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4 border-b dark:border-gray-700">
                <div class="w-full flex items-center space-x-3">
                    @if($settings['list'])
                        <a href="{{ route($settings['guard'].'.data.list', ['name' => $dataDefinition->name]) }}">
                    @endif
                        <h5 class="dark:text-white font-semibold flex items-center">
                            @if($settings['icon'])
                                <x-ui.icon :icon="$settings['icon']" class="inline-block w-5 h-5 mr-2 dark:text-white" />
                            @endif
                            @if($settings['overrideTitle'])
                                {!! $settings['overrideTitle'] !!}
                            @else
                                {!! $settings['title'] !!}
                            @endif
                        </h5>
                    @if($settings['list'])
                        </a>
                    @endif
                    @if(!$settings['overrideTitle'])
                        <div class="text-gray-400 font-medium">{{ trans('common.add_item') }}</div>
                    @endif
                </div>
                <div class="w-full flex flex-row items-center justify-end space-x-3">
                    @if($settings['list'])
                        <a href="{{ route($settings['guard'].'.data.list', ['name' => $dataDefinition->name]) }}"
                            class="w-full md:w-auto flex btn-dark text-sm px-3 py-2">
                            <x-ui.icon icon="left" class="h-3.5 w-3.5 mr-2" />
                            {{ trans('common.back_to_list') }}
                        </a>
                    @endif
                </div>
            </div>
            @php
            $hasTabs = !empty($form['tabs']);
            @endphp
            <div class="px-4 pb-4 @if($hasTabs) pt-1 sm:rounded-b-lg @endif">
                <x-forms.messages class="mt-4" />
                <x-forms.form-open :novalidate="$hasTabs" action="{{ route($settings['guard'].'.data.insert.post', ['name' => $dataDefinition->name]) }}"
                    enctype="multipart/form-data" id="formDataDefinition" method="POST" class="space-y-4 md:space-y-6" />
                @if ($form['columns'])
                    @if($hasTabs)
                        <x-ui.tabs :tabs="array_values($form['tabs'])" active-tab="1" class="space-y-4 md:space-y-6 py-6">
                        @php
                        $previousTab = null;
                        @endphp
                        @foreach ($form['columns'] as $column)
                            @if (!$column['hidden'])
                                @if($column['tab'] && $column['tab'] !== $previousTab)
                                    @if($previousTab !== null)
                                        </x-slot>
                                    @endif
                                    <x-slot :name="$column['tab']">
                                @endif

                                @if($column['container_start::insert'])
                                    <div class="{{ $column['container_start::insert'] }}">
                                @endif
                                @if($column['classes::insert'])
                                    <div class="{{ $column['classes::insert'] }}">
                                @endif
                                @include('data.form', compact('form', 'column'))
                                @if($column['classes::insert'])
                                    </div>
                                @endif
                                @if($column['container_end::insert'])
                                    </div>
                                @endif
                                @php
                                $previousTab = $column['tab'];
                                @endphp
                            @endif
                        @endforeach
                            </x-slot>
                        </x-ui.tabs>
                    @else
                        @foreach ($form['columns'] as $column)
                            @if (!$column['hidden'])
                                @if($column['container_start::insert'])
                                    <div class="{{ $column['container_start::insert'] }}">
                                @endif
                                @if($column['classes::insert'])
                                    <div class="{{ $column['classes::insert'] }}">
                                @endif
                                @include('data.form', compact('form', 'column'))
                                @if($column['classes::insert'])
                                    </div>
                                @endif
                                @if($column['container_end::insert'])
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @endif
                    <div class="flex flex-col items-center justify-evenly space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
                        <button type="submit" class="w-full btn-primary btn-lg">{{ trans('common.create') }}<span
                                class="form-dirty hidden">&nbsp;*</span></button>
                        @if($settings['list'])
                            <a href="{{ route($settings['guard'].'.data.list', ['name' => $dataDefinition->name]) }}"
                            class="btn btn-lg w-full">{{ trans('common.cancel') }}</a>
                        @endif
                    </div>
                @endif
                <x-forms.form-close />
                @if (session('current_tab_index'))
                    <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        window.openTab({{ session('current_tab_index') }});
                    });
                    </script>
                @endif
                @if ($errors->any())
                    <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        window.openTabWithInvalidElement();
                    });
                    </script>
                @endif
            </div>
        </div>
    </div>

@stop
