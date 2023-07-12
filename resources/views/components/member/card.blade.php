@if ($flippable && $authCheck)
<div x-data="{ flipped: false }">
@endif
    <div {{ $attributes->except('class') }} id="{{ $element_id }}_front"
        @if ($flippable && $authCheck) x-on:click="flipped = true" x-show="!flipped" @endif
        data-bg-color="{{ $bgColor }}" data-bg-opacity="{{ $bgColorOpacity / 100 }}" data-bg-image="{{ $bgImage }}"
        class="transform ring-0 @if ($flippable && $authCheck) hover:shadow-xl @endif shadow-md relative @if ($links) cursor-pointer @endif select-none border-0 dark:border-gray-300 border-gray-600 max-w-md mx-auto grid items-start grid-cols-1 gap-5 p-3 md:p-4 rounded-2xl bgColor_{{ $element_id }} textColor_{{ $element_id }} bg-fixed bg-center bg-cover bg-no-repeat {{ $attributes->get('class') }}">
        <div class="flex items-start">
            <div class="flex-grow min-w-0">
                <div class="flex items-center text-lg font-medium">
                    @if ($customLink)
                    <a href="{{ $customLink }}" class="after:absolute after:inset-0"> </a>
                    @endif
                    @if ($icon)
                        <span class="mr-2">
                            <x-ui.icon :icon="$icon" class="textColor_{{ $element_id }} w-5 h-5" />
                        </span>
                    @endif
                    @if (!$flippable && $links)
                        <a href="{{ route('member.card', ['card_id' => $id]) }}" class="after:absolute after:inset-0">
                    @endif
                    @if ($logo)
                        <img class="tracking-tight h-8" src="{{ $logo }}">
                    @else
                        <span class="tracking-tight truncate">{{ $contentHead }}</span>
                    @endif
                    @if (!$flippable)
                        </a>
                    @endif
                </div>
            </div>
            <div class="flex-none text-right w-24">
                @if ($flippable || $authCheck)
                    <div class="text-xs font-extralight textLabelColor_{{ $element_id }}">{{ trans('common.balance') }}</div>
                    @if ($authCheck)
                        <div class="flex items-center justify-end">
                            <x-ui.icon icon="coins" class="textColor_{{ $element_id }} w-4 h-4 mr-1" />
                            @if ($showBalance)
                                <div class="text-lg font-medium format-number">{{ $balance }}</div>
                            @else
                                <div class="text-lg font-medium">-</div>
                            @endif
                        </div>
                    @else
                        <a rel="nofollow" href="{{ route('member.login') }}" class="text-sm font-medium underline hover:underline after:absolute after:inset-0">{{ trans('common.log_in') }}</a>
                    @endif
                @endif
                @if (!$authCheck && !$flippable)
                    <div class="h-[40px]"></div>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="flex-grow mx-5 overflow-hidden">
                <h3 class="text-2xl font-extralight line-clamp-2 mb-2">{{ $contentTitle }}</h3>
                <div class="line-clamp-3 font-light text-sm">{{ $contentDescription }}</div>
            </div>
            <div class="flex-none">
                @if ($authCheck && $showQr)
                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="bg-gray-100 rounded-lg w-[112px] h-[112px] p-[5px] shadow"
                        data-qr-url="{{ $urlToEarnPoints }}" data-qr-color-light="{{ $qrColorLight }}"
                        data-qr-color-dark="{{ $qrColorDark }}" data-qr-scale="2"
                    />
                @else
                    <div class="rounded-lg w-[102px] h-[102px] ml-auto flex place-content-center items-center shadow"
                        style="background-color: {{ $qrColorLight }}">
                        <svg class="w-[96px] h-[96px]" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="{{ $qrColorDark }}">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                        </svg>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex self-end">
            <div class="flex-grow">
                <div class="text-xs font-extralight textLabelColor_{{ $element_id }}">{{ trans('common.identifier') }}</div>
                <div class="text-sm font-light">{{ $identifier }}</div>
            </div>
            <div class="flex-none w-24 text-right hidden md:block">
                <div class="text-xs font-extralight textLabelColor_{{ $element_id }}">{{ trans('common.issue_date') }}</div>
                <div class="text-sm font-light format-date" data-date="{{ $issueDate }}">&nbsp;</div>
            </div>
            <div class="flex-none w-24 text-right">
                <div class="text-xs font-extralight textLabelColor_{{ $element_id }}">{{ trans('common.expiration_date') }}</div>
                <div class="text-sm font-light format-date" data-date="{{ $expirationDate }}">&nbsp;</div>
            </div>
        </div>
    </div>

    @if ($flippable && $authCheck)
        <div id="{{ $element_id }}_back" x-show="flipped" data-bg-color="{{ $bgColor }}"
            data-bg-opacity="{{ $bgColorOpacity / 100 }}" data-bg-image="{{ $bgImage }}"
            class="transform ring-0 shadow-xl relative border-0 dark:border-gray-300 border-gray-600 max-w-md mx-auto grid items-start grid-cols-1 gap-5 p-3 md:p-4 rounded-2xl bgColor_{{ $element_id }} textColor_{{ $element_id }} bg-fixed bg-center bg-cover bg-no-repeat">
            <div class="flex">
                <div class="flex-1 text-xs sm:text-base">
                    <div class="mb-8">
                        <a href="javascript:void(0);" x-on:click="flipped = false">
                            <x-ui.icon icon="uturn" class="textColor_{{ $element_id }} w-8 h-8" />
                        </a>
                    </div>

                    <div class="flex items-center mb-4">
                        <x-ui.icon icon="card" class="textColor_{{ $element_id }} w-6 h-6 mr-2 sm:w-5 sm:h-5" />
                        {{ $identifier }}
                    </div>

                    <div class="flex items-center">
                        <x-ui.icon icon="user" class="textColor_{{ $element_id }} w-6 h-6 mr-2 sm:w-5 sm:h-5" />
                        {{ auth('member')->user()->unique_identifier }}
                    </div>

                </div>
                <div class="flex items-end">
                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                        x-on:click="flipped = false"
                        class="cursor-pointer bg-gray-100 rounded-lg w-[214px] h-[214px] shadow self-end my-[9px] mr-[9px]"
                        data-qr-url="{{ $urlToEarnPoints }}"
                        data-qr-color-light="{{ $qrColorLight }}"
                        data-qr-color-dark="{{ $qrColorDark }}"
                        data-qr-scale="8"
                    />
                </div>
            </div>
        </div>
    </div>
    @endif
<style type="text/css">
.bgColor_{{ $element_id }} {
    color: {{ $bgColor }};
}
.textColor_{{ $element_id }} {
    color: {{ $textColor }};
}
.textLabelColor_{{ $element_id }} {
    color: {{ $textLabelColor }};
}
</style>