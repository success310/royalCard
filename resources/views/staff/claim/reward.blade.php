@extends('staff.layouts.default')

@section('page_title', $card->head . config('default.page_title_delimiter') . trans('common.redeem_points_for_reward') .  config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
<div class="flex flex-col w-full p-6">
    <div class="space-y-6 h-full w-full place-items-center">
        <div class="max-w-md mx-auto">
            @if($member && $card)
                <x-forms.messages />
                <div class="max-w-md mx-auto">
                    <x-member.reward-card class="mb-6" :reward="$reward" />
                </div>
                @if($canRedeem)
                    <x-forms.form-open action="{{ route('staff.claim.reward.post', ['member_identifier' => $member->unique_identifier, 'card_id' => $card->id, 'reward_id' => $reward->id]) }}" enctype="multipart/form-data" method="POST" />
                        <div class="grid gap-4 sm:col-span-2 md:gap-6 sm:grid-cols-1 mb-6">
                            <x-forms.image
                                type="image"
                                capture="environment"
                                icon="camera"
                                name="image"
                                :placeholder="trans('common.add_photo')"
                                accept="image/*"
                            />
                        </div>

                        <div class="grid gap-4 sm:col-span-2 md:gap-6 sm:grid-cols-1 mb-6">
                            <x-forms.input
                                name="note"
                                value=""
                                type="text"
                                input-class="text-xl"
                                :placeholder="trans('common.optional_note')"
                                :required="false"
                            />
                        </div>

                        <div class="mb-6">
                            <button type="submit" class="btn-primary btn-lg w-full h-16">{{ trans('common.redeem_points_for_reward') }}</button>
                        </div>
                    <x-forms.form-close />
                @endif
            @endif

            @if($member)
               <x-member.member-card class="mb-6" :member="$member" />
            @else
                <div class="mb-6 format format-sm sm:format-base lg:format-md dark:format-invert">
                    <h3>{{ trans('common.member_not_found') }}</h3>
                </div>
            @endif

            @if($card)
                <x-member.card
                    :card="$card"
                    :member="$member"
                    :flippable="false"
                    :links="false"
                    :show-qr="false"
                />
                <a href="{{ route('member.card', ['card_id' => $card->id]) }}" target="_blank" class="mt-4 flex items-center text-link">
                    <x-ui.icon icon="arrow-top-right-on-square" class="w-5 h-5 mr-2"/>
                    {{ trans('common.view_card_on_website') }}
                </a>
            @else
                <div class="format format-sm sm:format-base lg:format-md dark:format-invert">
                    <h3>{{ trans('common.card_not_found') }}</h3>
                </div>
            @endif
        </div>
    </div>
</div>
@stop
