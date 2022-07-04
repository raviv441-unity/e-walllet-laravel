<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class TransactionHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'sender_id',
        'receiver_id',
        'amount',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::created(function ($transactionHistory) {
            $transactionHistory->transaction_id = Str::uuid();
            $transactionHistory->save();
        });
    }


    public function sender(){
        return $this->belongsTo('App\Models\User','sender_id','id');
    }

    public function receiver(){
        return $this->belongsTo('App\Models\User','receiver_id','id');
    }
}
