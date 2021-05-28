<?php

namespace App\Events;

use Auth;
use App\Idea;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IdeaCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Idea */
    private $idea;

    /**
     * Create a new event instance.
     *
     * @param  Idea $idea
     * @return void
     */
    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    /**
     * Get data the event should broadcast with.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->idea->id,
            'text' => \Str::limit($this->idea->text, 200),
            'time' => $this->idea->created_at->format('H:m'),

            'url' => route('ideas.show', $this->idea->id),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return User::where('id', '<>', Auth::id())->role('editor')->get()->map(function ($user) {
            return new PrivateChannel('App.User.' . $user->id);
        })->toArray();
    }
}
