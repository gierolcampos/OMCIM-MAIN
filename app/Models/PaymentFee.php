<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentFee extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_fees';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'fee_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purpose',
        'description',
        'total_price',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get active payment fees
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveFees()
    {
        return self::where('is_active', true)->orderBy('purpose')->get();
    }
}
