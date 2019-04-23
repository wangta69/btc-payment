<?php

namespace Pondol\BtcPayment\Commands;

use Pondol\BtcPayment\Bitcoind;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Pondol\BtcPayment\Events\ConfirmedPaymentEvent;
use Pondol\BtcPayment\Events\UnconfirmedPaymentEvent;
use Pondol\BtcPayment\Events\UnknownTransactionEvent;
use Pondol\BtcPayment\Models\Payment;
use Pondol\BtcPayment\Models\UnknownTransaction;

class CheckPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcoin:checkpayment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for bitcoin payments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Bitcoind $bitcoind)
    {
        // $bitcoind = resolve("LaravelBtcClient");
        $this->checkPayment($bitcoind);
    }

    private function checkPayment($bitcoind)
    {
        // get transaction from bitcoind
        // $transactions = $bitcoind->listtransactions('*', 50);
        $transactions = $bitcoind->listtransactions('*', 5); // 테스트를 용이하게 하기위해서 5개만 구한다. (나중에 되도록 많이)
        if (!is_array($transactions)) {
            $transactions = $transactions->get();
        }
        $transactions = array_reverse($transactions);
        // 트랜잭션이 카테고리인것만 체크
        $transactions = array_filter($transactions, function ($v) {
            return $v['category'] == 'receive';
        });
        // reindex array - only transactions which receive bitcoins
        $transactions = array_values($transactions);

        // print_r($transactions);
        // Prepayments without transaction - not paid yet
        // taid 가 없는 것 (아직 결제가 되지 않은 것만 체크)
        /*
        $prepayments_no_tx = Payment::unpaid()->get();
        foreach ($prepayments_no_tx as $prepayment_no_tx) {
            // check if there are multiple payments to same address
            $keys = array_keys(array_column($transactions, 'address'), $prepayment_no_tx['address']);
            // only way to pair blockchain transaction with our db is by wallet address and amount
            $pair_found = false;
            // txid 가 동일한 것이 있는지 체크 후 금액이 동일한지 체크
            foreach ($keys as $key) {
                if ($transactions[$key]['amount'] == $prepayment_no_tx->amount) {
                    $prepayment_no_tx->txid = $transactions[$key]['txid'];
                    $prepayment_no_tx->amount_received = $transactions[$key]['amount'];
                    $prepayment_no_tx->save();
                    event(new UnconfirmedPaymentEvent($prepayment_no_tx));
                    $pair_found = true;
                }
            }
            // wrong amount is paid - we dontß know for what order is that payment and this is unknown transaction
            // 동일한 것이 없으면 ..
            if (!$pair_found) {
                foreach ($keys as $key) {
                    $unknownTx = UnknownTransaction::find($transactions[$key]['txid']);
                    if (!$unknownTx) {
                        $unknownTx = new UnknownTransaction;
                        $unknownTx->address =  $transactions[$key]['address'];
                        $unknownTx->amount_received =  $transactions[$key]['amount'];
                        $unknownTx->txid = $transactions[$key]['txid'];
                        event(new UnknownTransactionEvent($unknownTx));
                    }
                    $unknownTx->confirmations = $transactions[$key]['confirmations'];
                    $unknownTx->save();
                }
            }
        }

        $prepayments = Payment::not_confirmed()->get();
        foreach ($prepayments as $prepayment) {
            $key = array_search($prepayment->txid, array_column($transactions, 'txid'));
            if ($key !== false) {
                $prepayment->confirmations = $transactions[$key]['confirmations'];
                // if we have min confirmations, payment is confirmed
                if ($prepayment->confirmations >= config('bitcoind.min-confirmations')) {
                    $prepayment->paid = 1;
                    event(new ConfirmedPaymentEvent($prepayment));
                }
                $prepayment->save();
            }
        }
        */
        echo "------------------";

    //     print_r($transactions);
        // received 된 내용중 account 가 존재 하면 db에 입력한다.
        // $prepayment->confirmations 에서 bitcoind.min-confirmations 보다 크면 wallet 으로 옮기고 결제완료 처리를 진행한다.
        //Check for Prepayments with transaction in blockchain (these are paid), but we need number of confirmations

        foreach ($transactions as $trans) {
            if ($trans['account']) {

                $payment = Payment::firstOrNew(['user_id' => $trans['account'], 'address'=>$trans['address'], 'txid' => $trans['txid']]);
                $payment->amount = $trans['amount'];
                $payment->confirmations = $trans['confirmations'];
                echo $trans['confirmations'].PHP_EOL;
                if ($trans['confirmations'] >= config('bitcoind.min-confirmations')) {
                    $payment->paid = 1;
                    event(new ConfirmedPaymentEvent($payment));
                    $bitcoind->setaccount($trans['address'],'');
                    //move this address to main wallet

                }

                $payment->save();
            }

            /*
            $key = array_search($prepayment->txid, array_column($transactions, 'txid'));
            if ($key !== false) {
                $prepayment->confirmations = $transactions[$key]['confirmations'];
                // if we have min confirmations, payment is confirmed
                if ($prepayment->confirmations >= config('bitcoind.min-confirmations')) {
                    $prepayment->paid = 1;
                    event(new ConfirmedPaymentEvent($prepayment));
                }
                $prepayment->save();
            }
            */
        }
    }
}
