<?php

namespace App\Listeners;

use Pondol\BtcPayment\Events\UnknownTransactionEvent;
use Illuminate\Support\Facades\Log;

class UnknownTransactionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        // 
    }

    /**
     * Handle the event.
     *
     * @param  UnknownTransactionEvent  $event
     * @return void
     */
    public function handle(UnknownTransactionEvent $event)
    {
        Log::debug('Unknown Transaction Listener: '. $event->unknownTx);
    }
}
