<?php

namespace Pondol\BtcPayment\Events;

use Pondol\BtcPayment\Models\Payment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UnconfirmedPaymentEvent
{
    use SerializesModels;

    public $unconfirmedPayment;
    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(Payment $unconfirmedPayment)
    {
        $this->unconfirmedPayment = $unconfirmedPayment;
        //Log::debug('UnconfirmedPaymentEvent constructor :'.$this->unconfirmedPayment);
    }
}
