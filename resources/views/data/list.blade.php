@php
if($settings['overrideTitle']) {
    $pageTitle = $settings['overrideTitle'];
} else {
    $pageTitle = $settings['title'];
}
@endphp

@extends($settings['guard'].'.layouts.default')

@section('page_title', $pageTitle . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
    <div class="w-full">
        <div class="relative bg-white shadow-md dark:bg-gray-800 sm:rounded-lg m-0 sm:m-4"
            @if ($settings['multiSelect']) x-data="{
                selectAll: false,
                selected: [],
                toggleAllCheckboxes() {
                    event.stopPropagation();
                    this.selectAll = !this.selectAll;
                    const checkboxes = document.querySelectorAll('#tableDataDefinition input[type=checkbox]:not(#checkbox-all)');
                    checkboxes.forEach((checkbox, index) => {
                        this.selected[index] = this.selectAll;
                    });
                },
                anySelected() {
                    return this.selected.some(item => item);
                }
            }" @endif>
            <div
                class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
                <div class="w-full flex items-center space-x-3">
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
                    <div class="text-gray-400 font-medium">
                        {{ trans('common.number_results', ['number' => $tableData['data']->total()]) }}
                    </div>
                    @if($settings['description'])
                    <div data-fb="tooltip" title="{!! $settings['description'] !!}">
                        <x-ui.icon icon="info" class="h-4 w-4 text-gray-400 hover:text-gray-500" />
                        <span class="sr-only">{{ trans('common.more_info') }}</span>
                    </div>
                    @endif
                    <?php /*

                    <div data-fb="tooltip" title="{{ trans('common.showing_results', ['n' => $tableData['data']->firstItem() ? $tableData['data']->firstItem() . ' ' . trans('common._to') . ' ' . $tableData['data']->lastItem() : $tableData['data']->count(), 'total' => $tableData['data']->total()]) }}">
                        <x-ui.icon icon="info" class="h-4 w-4 text-gray-400 hover:text-gray-500" />
                        <span class="sr-only">{{ trans('common.more_info') }}</span>
                    </div>
                    */ ?>
                </div>

                <div class="w-full flex flex-row items-center justify-end space-x-3">

                    <div class="w-full">
                        <form class="flex items-center">
                            <label for="tableDataDefinition-search" class="sr-only">{{ trans('common.search') }}</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <x-ui.icon icon="magnifying-glass" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                </div>
                                <input type="search" name="search" id="tableDataDefinition-search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="{{ trans('common.search') }}" value="{{ request()->get('search') }}">
                            </div>
                        </form>
                    </div>

                    @if ($settings['insert'])
                        <a href="{{ route($settings['guard'].'.data.insert', ['name' => $dataDefinition->name]) }}"
                            class="whitespace-nowrap w-full md:w-auto flex btn-primary text-sm px-3 py-2">
                            <x-ui.icon icon="plus" class="h-3.5 w-3.5 mr-2" />
                            {{ trans('common.add_new_item') }}
                        </a>
                    @endif
                    @if ($settings['export'])
                        <a href="{{ route($settings['guard'].'.data.export', ['name' => $dataDefinition->name]) }}" class="btn-dark text-sm px-3 py-2">
                            <x-ui.icon icon="export" class="h-3.5 w-3.5 mr-2" />
                            {{ trans('common.export') }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="overflow-x-auto">
                <x-forms.form-open id="formDataDefinition" method="POST" />
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="tableDataDefinition">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            @if ($settings['multiSelect'])
                                <th scope="col" class="p-4" @click="toggleAllCheckboxes()">
                                    <div class="flex items-center">
                                        <input @click="toggleAllCheckboxes()" x-bind:checked="selectAll"
                                            autocomplete="off" id="checkbox-all" type="checkbox"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-all" class="sr-only">checkbox</label>
                                    </div>
                                </th>
                            @endif
                            @if ($tableData['columns'])
                                @foreach ($tableData['columns'] as $column)
                                    @if (!$column['hidden'])
                                        <th scope="col"
                                            class="@if ($column['filter']) py-0 @else py-3 @endif px-6 whitespace-nowrap
                                @if ($column['type'] == 'avatar') text-center w-28 @endif
                                @if (in_array($column['type'], ['image', 'boolean', 'impersonate', 'qr'])) text-center @endif
                                @if ($column['type'] == 'number' || $column['format'] == 'number') text-right @endif
                                @if ($column['classes::list']) {{ $column['classes::list'] }} @endif
                                            ">
                                            @if ($column['filter'])
                                                <select onchange="reloadWithFilter('{{ $column['name'] }}', this.value)" name="{{ $column['name'] }}" id="{{ $column['name'] }}" class="block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                    <option value="0">{!! $column['text'] !!}</option>
                                                    <option disabled>---</option>
                                                    @foreach($tableData['filters'][$column['name']]['options'] as $id => $filter)
                                                        <option @if(request()->input('filter.' . $column['name']) == $id) selected @endif value="{{ $id }}">{!! $filter !!}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                @if ($column['sortable'])
                                                    <a href="?order={{ $column['name'] }}&orderDir={{ (request()->input('orderDir', null) == 'asc') ? 'desc' : 'asc' }}">{!! $column['text'] !!}
                                                        <x-ui.icon icon="sortable" class="h-4 w-4 ml-1 inline-block" />
                                                    </a>
                                                @else
                                                    {!! $column['text'] !!}
                                                @endif
                                            @endif
                                            </th>
                                    @endif
                                @endforeach
                            @endif
                            @if ($settings['hasActions'])
                                <th scope="col" class="px-6 py-3 text-right">
                                    {{ trans('common.actions') }}
                                </th>
                            @endif
                        </tr>
                    </thead>
                    @if ($tableData['data']->all() !== null)
                        <tbody>
                            @foreach ($tableData['data']->all() as $i => $row)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-100 @if (!$settings['multiSelect']) bg-white dark:bg-gray-800 dark:hover:bg-gray-900/50 @endif"
                                    @if ($settings['multiSelect']) :class="selected[{{ $i }}] ? 'bg-gray-200 hover:bg-gray-200 dark:bg-gray-900 dark:hover:bg-800' : 'bg-white dark:bg-gray-800 dark:hover:bg-gray-900/50'" @endif>
                                    @if ($settings['multiSelect'])
                                        <td class="w-4 p-4"
                                            @click="selected[{{ $i }}] = !selected[{{ $i }}]">
                                            <div class="flex items-center">
                                                <input id="select-row-{{ $i }}"
                                                    x-model="selected[{{ $i }}]" name="id[]"
                                                    value="{{ $row['id'] }}" autocomplete="off" type="checkbox"
                                                    class="item-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                <label for="select-row-{{ $i }}"
                                                    class="sr-only">checkbox</label>
                                            </div>
                                        </td>
                                    @endif
                                    @foreach ($tableData['columns'] as $column)
                                        @if (!$column['hidden'])
                                            <td @if ($settings['multiSelect'] && !in_array($column['type'], ['impersonate', 'qr'])) @click="selected[{{ $i }}] = !selected[{{ $i }}]" @endif
                                                class="px-6 py-4  cursor-default @if ($column['highlight']) font-medium text-gray-900 whitespace-nowrap dark:text-white @endif
                            @if (in_array($column['type'], ['boolean', 'impersonate', 'qr'])) text-center align-middle @endif
                            @if ($column['type'] == 'number' || $column['format'] == 'number') text-right @endif
                            @if ($column['classes::list']) {{ $column['classes::list'] }} @endif
                                                ">
                                                @if (in_array($column['type'], ['boolean', 'impersonate', 'qr'])) <div class="inline-block mx-auto"> @endif
                                                @if (in_array($column['type'], ['date_time'])) <span class="format-date-time"> @endif
                                                @if (in_array($column['type'], ['belongsTo', 'belongsToMany']) && strlen($row[$column['name']]) > 32)
                                                    <abbr title="{{ parse_attr($row[$column['name']]) }}">{{ substr($row[$column['name']], 0, 32) }}&hellip;</abbr>
                                                @else
                                                    {!! $row[$column['name']] !!}
                                                @endif
                                                @if (in_array($column['type'], ['boolean', 'impersonate', 'qr'])) </div> @endif
                                                @if (in_array($column['type'], ['date_time'])) </span> @endif
                                            </td>
                                        @endif
                                    @endforeach
                                    @if ($settings['hasActions'])
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex flex-nowrap justify-end space-x-2">
                                                @if ($settings['view'])
                                                    <a href="{{ route($settings['guard'].'.data.view', ['name' => $dataDefinition->name, 'id' => $row['id']]) }}" data-fb="tooltip" title="{{ trans('common.view') }}" class="whitespace-nowrap items-center flex px-2 py-2 text-xs text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"><x-ui.icon icon="magnifying-glass" class="h-3.5 w-3.5" /></a>
                                                @endif
                                                @if ($settings['edit'])
                                                    <a href="{{ route($settings['guard'].'.data.edit', ['name' => $dataDefinition->name, 'id' => $row['id']]) }}" data-fb="tooltip" title="{{ trans('common.edit') }}" class="whitespace-nowrap items-center flex px-2 py-2 text-xs text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-center dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900"><x-ui.icon icon="pencil" class="h-3.5 w-3.52" /></a>
                                                @endif
                                                @if ($settings['delete'])
                                                    <a href="javascript:void(0);" data-fb="tooltip" title="{{ trans('common.delete') }}"
                                                        class="whitespace-nowrap items-center flex px-2 py-2 text-xs text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900"
                                                        @click="deleteItem('{{ $row['id'] }}', '{{ $settings['subject_column'] ? str_replace("'", "\'", parse_attr($row[$settings['subject_column']])) : null }}')"><x-ui.icon icon="trash" class="h-3.5 w-3.5" /></a>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
                    @if($tableData['data']->total() == 0)
                        <tbody>
                            <td id="noResults" colspan="3" class="p-6 text-center">{{ trans('common.no_results_found') }}</td>
                        </tbody>
                        <script>
                            function setColspan() {
                                const table = document.getElementById('tableDataDefinition');
                                const colspan = table.rows[0].cells.length;
                                document.getElementById('noResults').setAttribute('colspan', colspan);
                            }
                            setColspan();
                        </script>
                    @endif
                </table>
                <x-forms.form-close />
            </div>

            @if (($tableData['data']->total() > 0 && $settings['multiSelect']) || $tableData['data']->hasPages())
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center">
                    <div class="m-4">
                        @if ($settings['multiSelect'])
                            <select id="table-with-selected" x-data x-bind:disabled="!anySelected()"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="">{{ trans('common.with_selected_') }}</option>
                                <option value="" disabled>---</option>
                                <option value="delete">{{ trans('common.delete') }}</option>
                            </select>
                        @endif
                    </div>
                    @if ($tableData['data']->hasPages())
                        <div class="lg:ml-auto m-4">
                            {{ $tableData['data']->onEachSide(5)->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            @endif

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

            @if ($settings['multiSelect'])
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        function handleWithSelected(selectedValue) {

                            @if ($settings['delete'])
                                if (selectedValue == 'delete') {
                                    appConfirm('{{ trans('common.confirm_deletion') }}',
                                        '{{ trans('common.confirm_deletion_selected_items') }}', {
                                            'btnConfirm': {
                                                'click': function() {
                                                    const form = document.getElementById('formDataDefinition');
                                                    form.action =
                                                        '{{ route($settings['guard'].'.data.delete.post', ['name' => $dataDefinition->name]) }}';
                                                    form.submit();
                                                }
                                            }
                                        });
                                }
                            @endif
                        }

                        // Attach the event listener to the select element
                        const selectElement = document.getElementById('table-with-selected');
                        selectElement.addEventListener('change', function(event) {
                            handleWithSelected(event.target.value);
                            event.target.value = '';
                        });
                    });
                </script>
            @endif
            <script>
                const searchInput = document.getElementById('tableDataDefinition-search');
                let lastValue = searchInput.value;
                let lastKeyPressed;

                searchInput.addEventListener('input', (event) => {
                    if (event.target.value === '' && lastKeyPressed !== 'Backspace' && lastKeyPressed !== 'Delete') {
                        document.location = '{{ route($settings['guard'].'.data.list', ['name' => $dataDefinition->name]) }}';
                    }
                    lastValue = event.target.value;
                });

                searchInput.addEventListener('keydown', (event) => {
                    lastKeyPressed = event.key;
                });
            </script>
            <script>
                function reloadWithFilter(columnName, selectedValue) {
                    let url = new URL(window.location.href);
                    
                    let filterKey = `filter[${columnName}]`;
                    let paramsToRemove = ['page'];

                    if (selectedValue == 0) {
                        paramsToRemove.push(filterKey);
                    }

                    // Remove the specified parameters
                    for (let param of paramsToRemove) {
                        url.searchParams.delete(param);
                    }

                    // Set the new filter and column parameters
                    if (selectedValue != 0) {
                        url.searchParams.set(filterKey, selectedValue);
                    }

                    // Reload the page with the updated URL
                    window.location.href = url.href;
                }
            </script>
        </div>

    </div>
@stop
