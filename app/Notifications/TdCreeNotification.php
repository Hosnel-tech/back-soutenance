<?php

namespace App\Notifications;

use App\Models\Td;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TdCreeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Td $td) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouveau TD créé')
            ->line('Un nouveau TD a été créé à partir de votre épreuve: ' . $this->td->titre)
            ->line('Statut: ' . $this->td->statut);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'td_id' => $this->td->id,
            'titre' => $this->td->titre,
            'statut' => $this->td->statut,
        ];
    }
}
