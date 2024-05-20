<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateRequestNotification extends Notification
{
    use Queueable;
    protected $user;
    protected $amount;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, $amount)
    {
        $this->user = $user;
        $this->amount = $amount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    // public function toDatabase( $notifiable)
    // {
    //     $user =Auth::user();
    //     $amount= $this->amount;

    //     return [
    //         'body'=>"Hi {$notifiable->name} {$user->name} need  you to lend {$amount}  ",
    //         'sender_id'=>$user->id,
    //         'amount'=>$amount,
    //     ];
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

    public function toArray(object $notifiable): array
    {
        $user = Auth::user();
        $from = $user->phone_number;
        $to = $notifiable->user_contact_mobile;
        $amount = $this->amount;
        return [
            'body' => "need  you to lend {$amount}  ",
            'sender' => $from,
                   'country_code'=>auth()->user()->country_code,

            'sender_id' => $user->id,
            'receiver_id' => $notifiable->id,
            'amount' => $amount,
            'type' => 'borrowRequest'

        ];

    }
}
