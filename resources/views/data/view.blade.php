@php
if($settings['overrideTitle']) {
    $pageTitle = $settings['overrideTitle'];
} else {
    $pageTitle = trans('common.view_item_', ['item' => $settings['subject_column'] ? parse_attr($form['data']->{$settings['subject_column']}) : trans('common.item')]) .
    config('default.page_title_delimiter') . $settings['title'];
}
@endphp

@extends($settings['guard'].'.layouts.default')

@section('page_title', $pageTitle . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
    <div class="w-full">
        <div class="relative bg-white shadow-md dark:bg-gray-800 m-0 sm:m-4 sm:rounded-lg">
            <div
                class="flex flex-col px-4 py-3 space-y-3 md:flex-row md:items-center md:justify-between md:space-y-0 md:space-x-4 border-b dark:border-gray-700">
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
                        <div class="text-gray-400 font-medium">
                            {{ trans('common.view_item_', ['item' => $settings['subject_column'] ? parse_attr($form['data']->{$settings['subject_column']}) : trans('common.item')]) }}
                        </div>
                    @endif
                </div>
                <div class="w-full flex flex-row items-center justify-end space-x-3">
                    @if($settings['list'])
                        <a href="{{ route($settings['guard'].'.data.list', ['name' => $dataDefinition->name]) }}"
                            class="w-full md:w-auto flex btn-dark text-sm px-3 py-2 whitespace-nowrap text-ellipsis">
                            <x-ui.icon icon="left" class="h-3.5 w-3.5 mr-2" />
                            {{ trans('common.back_to_list') }}
                        </a>
                    @endif
                    @if ($settings['edit'])
                        <a href="{{ route($settings['guard'].'.data.edit', ['name' => $dataDefinition->name, 'id' => $form['data']->id]) }}"
                            class="w-full md:w-auto flex btn-warning text-sm px-3 py-2">
                            <x-ui.icon class="h-3.5 w-3.5 mr-2" icon="pencil" />
                            {{ trans('common.edit') }}
                        </a>
                    @endif
                    @if ($settings['delete'])
                        <button type="button" class="w-full md:w-auto flex btn-danger text-sm px-3 py-2"
                            @click="deleteItem('{{ $form['data']->id }}', '{{ $settings['subject_column'] ? str_replace("'", "\'", parse_attr($form['data']->{$settings['subject_column']})) : null }}')">
                            <x-ui.icon class="h-3.5 w-3.5 mr-2" icon="trash" />
                            {{ trans('common.delete') }}
                        </button>
                    @endif
                </div>
            </div>
            <div class="text-gray-900 dark:text-white p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4 gap-y-8">
                @if ($form['columns'])
                    @foreach ($form['columns'] as $column)
                        @if (!$column['hidden'])
                            @if($column['container_start::view'])
                                <div class="{{ $column['container_start::view'] }}">
                            @endif
                            @if($column['classes::view'])
                                <div class="{{ $column['classes::view'] }}">
                            @endif
                            <div>
                                <div class="mb-2 font-semibold text-gray-900 dark:text-gray-400">{{ $column['text'] }}</div>
                                @if ($column['type'] == 'image' || $column['type'] == 'avatar')
                                    @if ($form['data']->{$column['name']})
                                        <script>
                                            let imgModalSrc_{{ $column['name'] }} = "{{ $form['data']->{$column['name']} }}";
                                            let imgModalDesc_{{ $column['name'] }} = "{{ parse_attr($column['text']) }}";
                                        </script>
                                        <a @click="$dispatch('img-modal', {  imgModalSrc: imgModalSrc_{{ $column['name'] }}, imgModalDesc: imgModalDesc_{{ $column['name'] }} })"
                                            class="cursor-pointer">
                                            <img src="{{ $form['data']->{$column['name']} !== null && $column['conversion'] !== null ? $form['data']->{$column['name'] . '-' . $column['conversion']} : $form['data']->{$column['name']} }}"
                                                alt="{{ parse_attr($column['text']) }}"
                                                class="h-auto max-w-xs {{ $column['type'] == 'avatar' ? 'rounded-full w-32 h-32' : 'rounded-lg' }}' shadow-xl dark:shadow-gray-800">
                                        </a>
                                    @else
                                        <x-ui.icon icon="no-symbol" class="w-5 h-5" />
                                    @endif
                                @elseif (in_array($column['type'], ['date_time']))
                                    <span class="format-date-time">{!! $form['data']->{$column['name']} !!}</span>
                                @elseif (in_array($column['format'], ['datetime-local']))
                                    <span class="format-date-time-local">{!! $form['data']->{$column['name']} !!}</span>
                                @else
                                    {!! $form['data']->{$column['name']} !!}
                                @endif
                            </div>
                            @if($column['classes::view'])
                                </div>
                            @endif
                            @if($column['container_end::view'])
                                </div>
                            @endif
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @if ($settings['delete'])
        <script>
            function deleteItem(id, item) {
                if (item == null) item = "{{ trans('common.this_item') }}";

                appConfirm('{{ trans('common.confirm_deletion') }}', _lang.delete_confirmation_text.replace(":item",
                    '<strong>' + item + '</strong>'), {
                    'btnConfirm': {
                        'click': function() {
                            // Submit form
                            const form = document.getElementById('formDataDefinition');
                            form.action =
                                '{{ route($settings['guard'].'.data.delete.post', ['name' => $dataDefinition->name]) }}/' + id;
                            form.submit();
                        }
                    }
                });
            }
        </script>
    @endif
    @stop
