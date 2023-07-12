@extends('staff.layouts.default')

@section('page_title', trans('common.transactions') . config('default.page_title_delimiter') . $card->head . config('default.page_title_delimiter') . $member->name . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
<div class="flex flex-col w-full p-6">
    <div class="space-y-6 h-full w-full place-items-center">
        <div class="max-w-md mx-auto">
            <x-forms.messages />
            @if($card)
                <x-member.card
                    :card="$card"
                    :member="$member"
                    :flippable="false"
                    :links="false"
                    :show-qr="false"
                />
                <a href="{{ route('member.card', ['card_id' => $card->id]) }}" target="_blank" class="mt-4 mb-4 flex items-center text-link">
                    <x-ui.icon icon="arrow-top-right-on-square" class="w-5 h-5 mr-2"/>
                    {{ trans('common.view_card_on_website') }}
                </a>
            @endif
            @if($card && $member)
                <a href="{{ route('staff.earn.points', ['member_identifier' => $member->unique_identifier, 'card_identifier' => $card->unique_identifier]) }}" class="mb-6 btn-primary btn-lg flex">{{ trans('common.add_transaction') }}</a>
            @endif
            @if($member)
               <x-member.member-card class="mb-6" :member="$member" />
               <x-member.history :card="$card" :show-notes="true" :show-attachments="true" :member="$member" />
            @endif
        </div>
    </div>
</div>
@stop
