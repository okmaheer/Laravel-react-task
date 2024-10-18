<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    use HasFactory;

    // The fillable attributes for mass assignment
    protected $fillable = ['name'];

    /**
     * Get the leads for the lead status.
     * 
     * A lead status has many leads (one-to-many relationship).
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
