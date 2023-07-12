@extends('staff.layouts.default')

@section('page_title', $card->head . config('default.page_title_delimiter') . trans('common.add_points_to_balance') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
<div class="flex flex-col w-full p-6">
    <div class="space-y-6 h-full w-full place-items-center">
        <div class="max-w-md mx-auto">
            @if($member && $card)
            <x-forms.messages />
            <x-forms.form-open action="{{ route('staff.earn.points.post', ['member_identifier' => $member->unique_identifier, 'card_identifier' => $card->unique_identifier]) }}" enctype="multipart/form-data" method="POST" />
                <div class="grid gap-4 sm:col-span-2 md:gap-6 sm:grid-cols-2 mb-6">
                    <div>
                        <x-forms.input
                            name="purchase_amount"
                            value=""
                            :label="trans('common.purchase_amount')"
                            type="number"
                            inputmode="decimal"
                            :prefix="$currency['symbol_native']"
                            :suffix="$card->currency"
                            affix-class="text-gray-400 dark:text-gray-500 text-xl"
                            input-class="text-xl"
                            :min="0"
                            :step="$currency['step']"
                            :placeholder="$currency['placeholder']"
                            :required="true"
                        />
                    </div>
                    <div>
                        <x-forms.input
                            name="points"
                            value=""
                            inputmode="numeric"
                            :label="trans('common.points')"
                            type="number"
                            icon="coins"
                            affix-class="text-gray-400 dark:text-gray-500 text-xl"
                            input-class="text-xl cursor-not-allowed"
                            :min="0"
                            step="1"
                            placeholder="0"
                            :required="false"
                            :readonly="true"
                        />
                    </div>
                </div>

                <div class="grid gap-4 sm:col-span-2 md:gap-6 sm:grid-cols-1 mb-6">
                    <x-forms.image
                        type="image"
                        capture="environment"
                        icon="camera"
                        name="image"
                        :placeholder="trans('common.add_photo_of_receipt')"
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
                    <button type="submit" class="btn-primary btn-lg w-full h-16">{{ trans('common.add_points_to_balance') }}</button>
                </div>
            <x-forms.form-close />

            <script>
            const currency_unit_amount = {{ $card->currency_unit_amount }};
            const points_per_currency = {{ $card->points_per_currency }};
            const min_points_per_purchase = {{ $card->min_points_per_purchase }};
            const max_points_per_purchase = {{ $card->max_points_per_purchase }};

            const purchaseAmountInput = document.getElementById('purchase_amount');
            const pointsInput = document.getElementById('points');

            purchaseAmountInput.addEventListener('input', function() {
                // update points when purchase_amount changes
                const pointsValue = Math.round((this.value / currency_unit_amount) * points_per_currency);

                // check if points are within the allowed range
                if (pointsValue >= min_points_per_purchase && pointsValue <= max_points_per_purchase) {
                    pointsInput.value = pointsValue;
                } else if (pointsValue < min_points_per_purchase) {
                    pointsInput.value = min_points_per_purchase;
                } else if (pointsValue > max_points_per_purchase) {
                    pointsInput.value = max_points_per_purchase;
                }
            });

            pointsInput.addEventListener('input', function() {
                // update purchase_amount when points changes
                const purchaseAmountValue = (this.value / points_per_currency) * currency_unit_amount;

                purchaseAmountInput.value = purchaseAmountValue;
            });
            </script>
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
