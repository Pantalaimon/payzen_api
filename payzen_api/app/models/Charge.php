<?php

/**
 * An Eloquent Model: 'Charge'
 *
 * @property integer $id
 * @property float $amount
 * @property string $currency
 * @property string $shop_id
 * @property string $shop_key
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Charge extends Eloquent {

    public static $snakeAttributes = true; // relation "avalaibleMethods" will be arrayed/jsoned as "available_methods"
    protected $visible = [
        'id',
        'status',
        'messages',
        'amount',
        'currency',
        'availableMethods' /*TODO ,'transactions'*/];

    protected $hidden = [
        'shop_key'
    ];

    const STATUS_CREATED = 'created';

    const STATUS_INCOMPLETE = 'incomplete';

    const STATUS_COMPLETE = 'complete';

    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'amount',
        'currency',
        'shop_id',
        'shop_key',
        'status'
    ];

    /**
     * For validation by auto-generated method
     */
    public static $rules = [
        'amount' => 'required|numeric|min:0.00001',
        'currency' => 'required|alphanum|size:3',
        'shop_id' => 'required|size:8',
        'shop_key' => 'required|size:16'
    ];

    public function contexts() {
        return $this->hasMany('Context');
    }

    public function availableMethods() {
        return $this->hasMany('AvailableMethod');
    }
    // TODO transactions

    public function updateFromContext(Context $context) {
        switch ($context->status) {
            case Context::STATUS_CREATED:
                break;
            case Context::STATUS_SUCCESS:
                $this->status = \Charge::STATUS_COMPLETE;
                break;
            case Context::STATUS_FAILURE:
                $this->status = \Charge::STATUS_INCOMPLETE;
                break;
            case Context::STATUS_CANCELLED:
                $this->status = \Charge::STATUS_INCOMPLETE;
                break;
            case Context::STATUS_LOCKED:
                $this->status = \Charge::STATUS_INCOMPLETE;
                break;
        }
    }
}
