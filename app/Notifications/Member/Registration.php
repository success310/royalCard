<?php

namespace App\Notifications\Member;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Crypt;

class Registration extends Notification implements ShouldQueue
{
    use Queueable;

    protected $email, $password, $guard, $from;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $email, string $password, string $guard = null, string $from = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->guard = $guard;
        $this->from = $from;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return ['mail'];
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

        $routeName = $this->guard . '.login';
        if (!in_array($this->guard, ['admin', 'partner', 'staff'])) {
            $routeName = 'member.login';
        }
    
        $routeParams = [
            'e' => Crypt::encryptString($this->email),
            'p' => Crypt::encryptString($this->password),
        ];
    
        if ($this->guard == 'member' && $this->from) {
            $routeParams['from'] = urlencode($this->from);
        }
    
        $loginLink = route($routeName, $routeParams);
    
        return (new MailMessage)
            ->theme('platform')
            ->from($mailFromAddress, $mailFromName)
            ->subject(trans('common.registration_subject'))
            ->greeting(trans('common.greeting'))
            ->line(trans('common.registration_body', ['password' => '**' . $this->password . '**']))
            ->action(trans('common.log_in'), $loginLink)
            ->line(trans('common.registration_subcopy'))
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
