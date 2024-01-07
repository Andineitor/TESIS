<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

     protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = $this->createUrl($this->token);

        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required.');
    }

    protected function createUrl($token)
    {
        // Personaliza la lógica para construir la URL según tus necesidades
        return 'https://example.com/reset-password' . $token;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
