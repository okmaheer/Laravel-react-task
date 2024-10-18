<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    // The fillable attributes for mass assignment
    protected $fillable = ['name', 'email', 'phone', 'lead_status_id'];

    /**
     * Get the lead status associated with the lead.
     * 
     * A lead belongs to a lead status (one-to-many relationship).
     */
    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class);
    }
}
