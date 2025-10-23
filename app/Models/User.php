<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Hidden attributes for serialization
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ==========================================================
    // RELATIONSHIPS
    // ==========================================================

    /**
     * Papers assigned to this user as a reviewer
     */
    public function assignedPapers()
    {
        return $this->belongsToMany(
            \App\Models\Paper::class,
            'assignments',      // Pivot table
            'reviewer_id',      // Foreign key on pivot for this model
            'paper_id'          // Foreign key on pivot for Paper
        );
    }

    /**
     * All review submissions made by this user
     */
    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'reviewer_id', 'id');
    }

    /**
     * Conferences this user is assigned to as a reviewer
     */
    public function assignedConferences()
    {
        return $this->belongsToMany(
            \App\Models\Conference::class,
            'conference_reviewer',  // Pivot table
            'reviewer_id',          // Foreign key on pivot for User
            'conference_code',      // Foreign key on pivot for Conference
            'id',                   // Local key on User model
            'conference_code'       // Local key on Conference model
        )->withTimestamps();
    }
}
