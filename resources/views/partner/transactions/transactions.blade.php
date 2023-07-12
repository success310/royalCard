@extends('partner.layouts.default')

@section('page_title', trans('common.transactions') . config('default.page_title_delimiter') . $card->head . config('default.page_title_delimiter') . $member->name . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
<div class="flex flex-col w-full p-6">
    <div class="space-y-6 h-full w-full place-items-center">
        <div class="max-w-md mx-auto">
            <div class="flex items-center mb-6">
                <a href="{{ route('partner.data.list', ['name' => 'members']) }}"
                    class="flex btn-dark btn-md mr-3 whitespace-nowrap text-ellipsis">
                    <x-ui.icon icon="user-group" class="h-5 w-5 mr-2" />
                    {{ trans('common.members') }}
                </a>
                <a href="{{ route('member.card', ['card_id' => $card->id]) }}" target="_blank" class="flex items-center text-link">
                    <x-ui.icon icon="arrow-top-right-on-square" class="w-5 h-5 mr-2"/>
                    {{ trans('common.view_card_on_website') }}
                </a>
            </div>
            <x-forms.messages />
            @if($card)
                <x-member.card
                    :card="$card"
                    :member="$member"
                    :flippable="false"
                    :links="false"
                    :show-qr="false"
                />
            @endif
            @if($member)
               <x-member.member-card class="my-6" :member="$member" />
               <x-member.history :card="$card" :show-notes="true" :show-attachments="true" :show-staff="true" :member="$member" />
            @endif
        </div>
    </div>
</div>
@stop
