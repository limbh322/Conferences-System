<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceReviewer extends Model
{
    use HasFactory;

    protected $fillable = ['conference_code', 'reviewer_name'];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_code', 'conference_code');
    }
}
