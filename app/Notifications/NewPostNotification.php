<?php

namespace App\Notifications;

use App\Events\PostWasCreated;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewPostNotification extends Notification
{
    public $post;

    public $user;

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event
     */
    public function handle(PostWasCreated $event)
    {
        $this->post = $event->post;
        $this->user = $event->user;

        $admins = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('role_id', 1);
            })->get();

        app(\Illuminate\Contracts\Notifications\Dispatcher::class)
            ->sendNow($admins, $this);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->greeting('Hi there!')
            ->line("A new post has been created by {$this->user->name} titled, {$this->post->title}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
