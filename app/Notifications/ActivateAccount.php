<?php

namespace Valda\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ActivateAccount extends Notification
{
    /**
     * The user's email.
     *
     * @var string
     */
    public $email;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $email
     * @param  string  $token
     * @return void
     */
    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $routeUrl = route(config('app.url') . route('account.activate', [
            'email' => $this->email,
            'token' => $this->token,
        ], false));

        return (new MailMessage)
            ->line('You are almost there! Click the button below to activate your account.')
            ->action('Activate Account', url(config('app.url') . $routeUrl));
    }
}
