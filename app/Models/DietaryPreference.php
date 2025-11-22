<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietaryPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get all foods with this dietary preference
     */
    public function foods()
    {
        return $this->belongsToMany(Food::class);
    }

    /**
     * Scope for active preferences only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope by type (diet, religious, allergy, other)
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
