<?php

/**
 * An Eloquent Model: 'Currency'
 *
 * @property integer $id
 * @property string $alpha3
 * @property string $numeric
 * @property integer $multiplicator
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Currency extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'alpha3' => 'required',
		'numeric' => 'required',
		'multiplicator' => 'required'
	);
}
