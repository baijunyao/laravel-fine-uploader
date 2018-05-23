<?php

namespace Baijunyao\LaravelFineUploader\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Uploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $file;

    /**
     * Uploaded constructor.
     *
     * @param array $file
     */
    public function __construct(array $file)
    {
        $this->file = $file;
    }
}
