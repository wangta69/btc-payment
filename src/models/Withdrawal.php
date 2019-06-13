<?php
namespace Pondol\BtcPayment\Models;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = ['user_id', 'address', 'txid'];//

    const UPDATED_AT = null;
    public function user()
    {
        return $this->belongsTo(config('bitcoind.user'));
    }
}
