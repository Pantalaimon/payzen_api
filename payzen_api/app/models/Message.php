<?php

/**
 * An Eloquent Model: 'Message'
 *
 * @property integer $id
 * @property integer $charge_id
 * @property string $title
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Message extends Eloquent {

    protected $visible = [
        'title',
        'description'
    ];

    protected $guarded = [];

    public static $rules = array(
        'charge_id' => 'required',
        'title' => 'required',
        'description' => 'required'
    );

    public function charge() {
        return $this->belongsTo('Charge', 'charge_id');
    }
}
