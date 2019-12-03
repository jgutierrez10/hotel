<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Reservation extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    public $table = 'reservation';

    public $fillable = ['from_date', 'to_date', 'status_id', 'customer_id', 'partner_id', 'payment_option_id', 'warranty_option_id', 'total_to_bill', 'currency_id', 'comments'];

    public static $rules = [
        'fromDate' => 'required|date|after_or_equal:today',
        'toDate' => 'required|date|after_or_equal:fromDate',
        'customer' => 'required|array',
        'rooms' => 'required|array',
        'isPartner' => 'required|boolean',
        //'paymentOption' => 'required',
        //'warranty' => 'required',
        'creditCardId' => 'required_if:warranty,6',
        'creditCardNumber' => 'required_if:warranty,6',
        'creditCardExpirationDate' => 'required_if:warranty,6',
        //'total_to_bill' => 'numeric'
    ];

    public static $messages = [
        'fromDate.required' => 'El campo "Fecha de Entrada" es obligatorio.',
        'fromDate.date' => 'El campo "Fecha de Entrada" debe ser una fecha válida.',
        'fromDate.after_or_equal' => 'El campo "Fecha de Entrada" debe ser una fecha igual o mayor al día de hoy.',
        'toDate.required' => 'Debe ingresar la fecha de salida de la reserva.',
        'toDate.date' => 'El campo "Fecha de Salida" debe ser una fecha válida.',
        'toDate.after_or_equal' => 'El campo "Fecha de Salida" debe ser mayor o igual a la fecha de entrada.',
        'customer.required' => 'Debe seleccionar un cliente.',
        'customer.array' => 'El cliente seleccionado es inválido.',
        'rooms.required' => 'Debe seleccionar AL MENOS una habitación.',
        'rooms.array' => 'La habitación seleccionada no es válida.',
        'isPartner.required' => 'Debe indicar si el cliente es afiliado o particular.',
        'isPartner.boolean' => 'Debe indicar si el cliente es afiliado o particular.',
        //'paymentOption.required' => 'Debe ingresar el método de pago',
        //'warranty.required' => 'Debe ingresar el tipo de garantía',
        'creditCardId.required_if' => 'Debe seleccionar una tarjeta de crédito',
        'creditCardNumber.required_if' => 'Debe ingresar el número de la tarjeta de crédito',
        'creditCardExpirationDate.required_if' => 'Debe ingresar la fecha de vencimiento de la tarjeta de crédito',
        'creditCardSecurityNumber.required_if' => 'Debe ingresar el código de seguridad de la tarjeta de crédito',
        'id.required' => 'Error al actualizar la reserva',
        //'total_to_bill.numeric' => 'El campo "Total de la Reserva" es requerido y debe ser un número.'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function status()
    {
        return $this->belongsTo(reservationStatus::class, 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function partner()
    {
        return $this->belongsTo(Partners::class, 'partner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function reservedRooms()
    {
        return $this->hasMany(ReservedRoom::class, 'reservation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function companion()
    {
        return $this->hasMany(ReservationCompanion::class, 'reservation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function paymentOption()
    {
        return $this->belongsTo(PaymentOption::class);
    }
    public function warrantyOption()
    {
        return $this->belongsTo(WarrantyOption::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function creditCardWarranty()
    {
        return $this->hasOne(CcReservationWarranty::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'reserved_room');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    static function betweenDates($from, $to)
    {
        return Reservation::whereBetween('from_date',[$from,$to])->orWhereBetween('to_date',[$from,$to])
        ->with(['reserved_rooms', 'rooms'])
        ->get();
    }
}
