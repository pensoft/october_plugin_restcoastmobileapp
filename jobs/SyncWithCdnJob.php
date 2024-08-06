<?php

namespace Pensoft\RestcoastMobileApp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Event;

class SyncWithCdnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;
    protected $data;
    protected $deleted;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($event, $data, $deleted)
    {
        $this->event = $event;
        $this->data = $data;
        $this->deleted = $deleted;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Event::fire(
            new $this->event($this->data, $this->deleted)
        );
    }
}
