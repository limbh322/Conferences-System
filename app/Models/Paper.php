<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    protected $primaryKey = 'paper_id'; // Primary key
    protected $fillable = [
        'title', 'abstract', 'keywords', 'file_path', 'author_id', 'conference_code', 'status'
    ];

    // Author relationship
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Conference relationship
    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_code', 'conference_code');
    }

    // Reviews relationship
    public function reviews()
    {
        return $this->hasMany(Review::class, 'paper_id', 'paper_id');
    }

    // Assignments relationship (for assigned reviewers)
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'paper_id', 'paper_id');
    }

    // Assigned reviewers relationship (via assignments)
    public function assignedReviewers()
    {
        return $this->belongsToMany(User::class, 'assignments', 'paper_id', 'reviewer_id');
    }
    
}
