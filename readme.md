# btc-payment

## Prerequisites
**Laravel version >= 5.5**

Install package via composer:
```
composer require wangta69/btc-payment
```

```
$btc = app("Pondol\BtcPayment\Bitcoind");
protected $btc;
/**
 * Create a new controller instance.
 *
 * @return void
 */
public function __construct(Bitcoind $btc)
{
    $this->btc = $btc;
    $this->btc->getaccountaddress("");
}
```
OR
```
public function getaccountaddress() {
    $btc = app("Pondol\BtcPayment\Bitcoind");
    return $btc->getaccountaddress("37");
}
```
