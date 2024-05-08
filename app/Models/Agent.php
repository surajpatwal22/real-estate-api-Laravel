<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','name','	email','contact_no','address','profile','aadhar_img','agency_name','license_number','experience_years','bio','availability','status','entry_by','state'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    
    }
}
