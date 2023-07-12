<?php

namespace App\Notifications\Member;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Card;
use App\Models\Member;
use NumberFormatter;

class PointsReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $member, $points, $card;

    /**
     * Create a new notification instance.
     */
    public function __construct(Member $member, string $points, Card $card)
    {
        $this->member = $member;
        $this->points = $points;
        $this->card = $card;
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
        $cardLink = route('member.card', ['card_id' => $this->card->id]);

        return (new MailMessage)
            ->theme('platform')
            ->from($mailFromAddress, $mailFromName)
            ->subject(trans('common.points_received_subject', ['points' => $points]))
            ->greeting(trans('common.greeting'))
            ->line(trans('common.points_received_body', ['points' => '**' . $points . '**']))
            ->action(trans('common.points_received_cta'), $cardLink)
            ->line(trans('common.points_received_subcopy'))
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
