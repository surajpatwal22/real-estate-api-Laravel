<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
    ];
    public function subcategory() {
        return $this->hasMany(Subcategory::class);
    }
    public function property() {
        return $this->hasMany(Property::class);
    }
}
