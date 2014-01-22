<?php

class Transaction extends Eloquent {

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        "charge_id"
    ];

    public static $rules = array(
        'charge_id' => 'required',
        'trans_date' => 'required',
        'trans_id' => 'required'
    );

    public static function buildTransaction(Charge $charge, Context $context) {
        $transaction = new Transaction();
        $transaction->charge_id = $charge->id;
        $transaction->trans_id = $context->trans_id;
        $transaction->trans_date = $context->trans_date;
        return $transaction;
    }

    public function charge() {
        return $this->belongsTo('Charge', 'charge_id');
    }
}
