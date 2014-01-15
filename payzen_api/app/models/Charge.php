<?php

class Charge extends Eloquent {

    // protected $guarded = [];
    protected $fillable = [
        'amount',
        'currency',
        'shop_id',
        'shop_key'
    ];

    public static $rules = [
        'amount' => 'required',
        'currency' => 'required',
        'shop_id' => 'required',
        'shop_key' => 'required',
        'status' => 'required'
    ];
}
