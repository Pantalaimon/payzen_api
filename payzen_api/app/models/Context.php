<?php
/**
 * An Eloquent Model: 'Context'
 *
 * @property integer $charge_id
 * @property string $status
 * @property string $trans_date
 * @property string $trans_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $trans_time
 * @property string $cache_id
 * @property string $locale
 */
class Context extends Eloquent {
	const STATUS_CREATED = "created";
	const STATUS_SUCCESS = "success";
	const STATUS_FAILURE = "failure";
	const STATUS_LOCKED = "locked";
	const STATUS_CANCELLED = "cancelled";

	protected $hidden = [
			"charge_id"
	];


}