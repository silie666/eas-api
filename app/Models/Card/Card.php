<?php

namespace App\Models\Card;

use App\Models\Model;
use App\Models\User\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;

    const MODEL_NAME = '信用卡';
    protected $fillable = [
        'brand_name',
        'number',
        'expiration_date',
        'card_table_id',
        'card_table_type',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function student()
    {
        $this->morphTo()->where('card_table_type', Student::class);
    }
}