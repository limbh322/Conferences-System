<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Primary key
    protected $primaryKey = 'review_id';

    // Auto-incrementing primary key
    public $incrementing = true;

    // Mass assignable fields
    protected $fillable = [
        'paper_id',
        'reviewer_id',
        'conference_id',  
        'status',          // Added status
        'score',           // numeric 0–10
        'comments',        // review comment
        'recommendation',  // recommendation value
    ];

    // Paper relationship
    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id', 'id'); 
    }

    // Reviewer relationship
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id', 'id');
    }

    // Conference relationship
    public function conference()
    {
        return $this->belongsTo(\App\Models\Conference::class, 'conference_id', 'id');
    }
}
