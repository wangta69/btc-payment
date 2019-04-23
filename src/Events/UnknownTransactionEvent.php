<?php

namespace Pondol\BtcPayment\Events;

use Pondol\BtcPayment\Models\UnknownTransaction;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UnknownTransactionEvent
{
    use SerializesModels;

    public $unknownTx;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(UnknownTransaction $unknownTx)
    {
        $this->unknownTx = $unknownTx;
        //Log::debug('UnknownTransaction Event constructor :'.$this->$unknownTx);
    }
}
