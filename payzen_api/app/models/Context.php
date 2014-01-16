<?php
use PayzenApi\PageInfo;

/**
 * An Eloquent Model: 'Context'
 * FIXME trans_time duplicates trans_date !
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
 * @property integer $id
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

    public function charge() {
        return $this->belongsTo('Charge', 'charge_id');
    }

    public function updateFromPageInfo(PageInfo $pageInfo) {
        switch ($pageInfo->state) {
            case PageInfo::STATE_CHOICE:
            case PageInfo::STATE_ENTRY:
                // ignore
                break;
            case PageInfo::STATE_UNKNOWN:
                $this->status = self::STATUS_LOCKED;
                break;
            case PageInfo::STATE_SUCCESS:
                $this->status = self::STATUS_SUCCESS;
                break;
            default:
                $this->status = self::STATUS_CANCELLED;
        }
    }
}