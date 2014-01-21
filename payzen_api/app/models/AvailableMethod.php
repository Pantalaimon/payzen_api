<?php

/**
 * An Eloquent Model: 'AvailableMethod'
 *
 */
class AvailableMethod extends Eloquent {

    protected $table = 'availablemethods';

    protected $fillable = [
        'method'
    ];

    protected $visible = [
        'method'
    ];

    public static $rules = array(
        'charge_id' => 'required',
        'method' => 'required'
    );

    public function charge() {
        return $this->belongsTo('Charge', 'charge_id');
    }
}
