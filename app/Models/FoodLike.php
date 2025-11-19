<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'food_id' => 'integer',
    ];

    /**
     * Get the user who liked the food
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the food that was liked
     */
    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
