<?php

/**
 * An Eloquent Model: 'UsedMethod'
 *
 */
class UsedMethod extends Eloquent {

    protected $table = 'usedmethods';

    protected $guarded = array();

    public static $rules = array(
        'charge_id' => 'required',
        'method' => 'required'
    );

    public function charge() {
        return $this->belongsTo('Charge', 'charge_id');
    }
}
