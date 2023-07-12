@extends('member.layouts.default')

@section('page_title', config('default.app_name'))

@section('content')
    <div class="flex flex-col w-full m-6">
        <div class="space-y-6 h-full w-full place-items-center">
            @if (!$cards || $cards->isEmpty())
                <div class="grid space-y-6 h-full w-full place-items-center text-gray-900 dark:text-white">
                    <div class="w-80 my-20">
                        <x-ui.icon icon="scan-qr" class="h-40 w-40 mx-auto" />
                        <div class="mt-5 text-center text-2xl font-semibold">
                            {{ trans('common.no_cards_collected_yet') }}
                        </div>
                        @if(!auth('member')->check())
                        <div class="text-center mt-2">{!! trans('common.or_log_in_to_view', ['link' => '<a href="'.route('member.login').'" class="text-link">' . trans('common._log_in_') . '</a>']) !!}</div>
                        @endif
                    </div>
                </div>
            @else
                @foreach($cards as $card)
                    <x-member.card
                        :card="$card"
                    />
                @endforeach
            @endif
        </div>
    </div>
@stop
