<?php

namespace App\Notifications\Member;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Card;
use App\Models\Reward;
use App\Models\Member;
use NumberFormatter;

class RewardClaimed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $member, $points, $card, $reward;

    /**
     * Create a new notification instance.
     */
    public function __construct(Member $member, string $points, Card $card, Reward $reward)
    {
        $this->member = $member;
        $this->points = $points;
        $this->card = $card;
        $this->reward = $reward;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return (config('default.app_demo')) ? [] : ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $mailFromAddress = config('default.mail_from_address');
        $mailFromName = config('default.mail_from_name');

        $locale = $this->member->preferredLocale();
        $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $points = $formatter->format($this->points);
        $rewardLink = route('member.card.reward', ['card_id' => $this->card->id, 'reward_id' => $this->reward->id]);

        return (new MailMessage)
            ->theme('platform')
            ->from($mailFromAddress, $mailFromName)
            ->subject(trans('common.reward_claimed_subject', ['reward_title' => $this->reward->title]))
            ->greeting(trans('common.greeting'))
            ->line(trans('common.reward_claimed_body', ['reward_title' => '**' . $this->reward->title . '**', 'points' => '**' . $points . '**']))
            ->action(trans('common.reward_claimed_cta'), $rewardLink)
            ->line(trans('common.reward_claimed_subcopy'))
            ->salutation(trans('common.salutation'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toArray($notifiable): array
    {
        return [
            // Additional data if needed
        ];
    }
}
