<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Revision extends Model
{
    use HasFactory;

    protected $table = 'revisionables';

    protected $fillable = [
        'show',
        'created_by',
        'model_id',
        'revisionables_id',
        'revisionables_type',
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d h:i',
    ];

    /* ==================================================================== */
    /* =========================== Relationships ========================== */
    /* ==================================================================== */

    public function revisionable()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
