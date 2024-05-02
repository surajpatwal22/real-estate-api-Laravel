<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'category_id',
        'entry_by'
    ];

    public function category()
{
    return $this->belongsTo(Category::class);

}

public function property() {
    return $this->hasMany(Property::class);
}
}
