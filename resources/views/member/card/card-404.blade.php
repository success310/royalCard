@extends('member.layouts.default')

@section('page_title', trans('common.page_not_found') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')

    <div class="flex flex-col w-full p-6">
        <div class="grid- space-y-6 h-full w-full place-items-center">
            <div class="grid space-y-6 h-full w-full place-items-center text-gray-900 dark:text-white ">
                <div class="w-80">
                    <x-ui.icon icon="card" class="h-40 w-40 mx-auto"/>
                    <div class="mt-2 text-center text-2xl font-semibold">
                        {{ trans('common.no_card_found') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
