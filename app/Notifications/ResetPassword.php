<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends Notification
{
    use Queueable;
    public $token;



    /**
     * Create a new notification instance.
     */
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
    protected function buildMailMessage($url)
    {
        return(new MailMessage)
            ->subject('Notificación de restablecimiento de contraseña')
            ->line('Estás recibiendo este correo electrónico porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace de restablecimiento de contraseña caducará en :count minutos.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')])
            ->line('Si no solicitaste un restablecimiento de contraseña, no es necesario que realices ninguna otra acción.');

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
