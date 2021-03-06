<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentOption extends Model
{
    public $timestamps = false;

    public $table = 'payment_options';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'payment_option_id');
    }
    static function toScheduler()
    {
        return PaymentOption::select(DB::raw('id as value, description as label'))
            ->get()
            ->toArray();
    }
}
