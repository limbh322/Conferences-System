<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;

    /**
     * Use conference_code as the primary key
     */
    protected $primaryKey = 'conference_code';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'conference_code',
        'title',
        'description',
        'deadline',
    ];

    /**
     * ============================
     * Relationships
     * ============================
     */

    // Papers submitted to this conference
    public function papers()
    {
        return $this->hasMany(\App\Models\Paper::class, 'conference_code', 'conference_code');
    }

    // Reviewers assigned to this conference (many-to-many)
    public function reviewers()
    {
        return $this->belongsToMany(
            \App\Models\User::class,          // related model
            'conference_reviewer',            // pivot table name
            'conference_code',                // this model foreign key in pivot
            'reviewer_id',                    // related model foreign key in pivot
            'conference_code',                // local key on this model
            'id'                              // local key on User model
        )->withTimestamps();
    }

    // Optional: all reviews submitted (if you have a reviews table)
    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'conference_code', 'conference_code');
    }
}
