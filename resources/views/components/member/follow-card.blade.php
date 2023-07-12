@if($canFollow)
    @if(!$follows)
    <x-ui.button
        {{ $attributes }}
        rel="nofollow"
        :href="route('member.card.follow', ['card_id' => $card->id])"
        :text="($card->initial_bonus_points > 0) ? trans('common.start_saving_and_earn', ['initial_bonus_points' => '<span class=\'format-number replace-container\'>' . $card->initial_bonus_points . '</span>']) : trans('common.start_saving')"
        icon="bookmark"
    />
    @else
    <x-ui.button
        {{ $attributes }}
        rel="nofollow"
        onclick="unfollowCard()"
        :text="trans('common.unfollow_card')"
        icon="bookmark-slash"
    />

    <script>
        function unfollowCard() {
            appConfirm(@json(trans('common.unfollow_card'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), @json(trans('common.unfollow_confirm'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE), {
                'btnConfirm': {
                    'click': function() { 
                        document.location = '{{ route('member.card.unfollow', ['card_id' => $card->id]) }}';
                    }
                }
            });
        }
        </script>
    @endif
@endif