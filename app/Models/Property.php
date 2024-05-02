<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'user_id',
        'property_name','property_price','property_address','property_district','property_state','property_pin','property_long','property_lat','property_landmark','property_facing','owner_name','owner_contact','bulidup_area','floor','security_amt','society','no_of_beds','no_of_kitchen','no_of_bathroom','car_parking'
        ,'water','invertor','security','availability_status','furniture_status','status','images','entry_date','reason'
    ];

    protected $appends = ['picture_urls'];

public function getPictureUrlsAttribute(){
    $imagePaths = json_decode($this->images, true);
    $urls = [];
    foreach($imagePaths as $path) {
        $urls[] = url($path);
    }
    return $urls;
}
public function category()
{
    return $this->belongsTo(Category::class);

}

public function subcategory()
{
    return $this->belongsTo(Subcategory::class);

}

public function user()
{
    return $this->belongsTo(User::class);

}
}
