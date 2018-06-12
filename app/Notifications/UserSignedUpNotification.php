<?php

namespace App\Notifications;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserSignedUpNotification extends Notification
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'slack', 'nexmo'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('A new user has signed up!')
                    ->line("{$this->user->name} has just signed up on the LaravelUK website. Be nice and say hello!");
    }

    /**
     * @param $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $user = $this->user;

        return (new SlackMessage)
            ->success()
            ->from('LaravelUK', ':laraveluk:')
            ->to('#notification_demo')
            ->content('A new member has signed up')
            ->attachment(function ($attachment) use ($user) {
                $attachment->title('New Member', $user->id)
                    ->fields([
                        'Name' => $user->name,
                        'Joined' => Carbon::now()->toDateTimeString(),
                    ]);
            });
    }


    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
            ->content('A new user has signed up on LaravelUK. Be nice and go say hello!');
    }
}
