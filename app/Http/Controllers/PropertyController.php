<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    public function addProperty(Request $request){

        // dd($request->images);
        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'property_name' => 'required',
            'property_price' => 'required',
            'property_address' => 'required',
            'property_district' => 'required',
            'property_state' => 'required',
            'property_pin' => 'required',
            'property_long' => 'required',
            'property_lat' => 'required',
            'property_landmark' => 'required',
            'property_facing' => 'required',
            'bulidup_area' => 'required',
            'security_amt' => 'required',
            'availability_status' => 'required',
            'furniture_status' => 'required',
            'buyRentStatus' => 'required',
            'images' => '',
        ]);
        if($validator->fails()){
            return response()->json([
                'error'=> $validator->errors(),
                'status' => 400 ,
                'success' => false
            ],400);
        }else{
            $userid = Auth::user()->id;
            $property = new Property();
            $property->category_id = $request->category_id;
            $property->subcategory_id = $request->subcategory_id;
            $property->user_id = $userid;
            $property->property_name = $request->property_name;
            $property->property_price = $request->property_price;
            $property->property_address = $request->property_address;
            $property->property_district = strtolower($request->property_district);
            $property->property_state = $request->property_state;
            $property->property_pin = $request->property_pin;
            $property->property_long = $request->property_long;
            $property->property_lat = $request->property_lat;
            $property->property_landmark = $request->property_landmark;
            $property->property_facing = $request->property_facing;
            $property->owner_name = $request->owner_name;
            $property->owner_contact = $request->owner_contact;
            $property->bulidup_area = $request->bulidup_area;
            $property->security_amt = $request->security_amt;
            $property->floor = $request->floor;
            $property->society = $request->society;
            $property->no_of_beds = $request->no_of_beds;
            $property->no_of_kitchen = $request->no_of_kitchen;
            $property->no_of_bathroom = $request->no_of_bathroom;
            $property->car_parking = $request->car_parking;
            $property->water = $request->water;
            $property->invertor = $request->invertor;
            $property->security = $request->security;
            $property->availability_status = $request->availability_status;
            $property->furniture_status = $request->furniture_status;
            $property->rent_buy_status = $request->buyRentStatus;
            $property->property_description	 = $request->property_description	;
            $property->owner_whatsapp = $request->owner_whatsapp;
            $property->status = 2;
            $data = [];
            if($request->hasfile('images')) {
                foreach($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imageName = str_replace(' ', '_', $imageName);
                    $imagePath = public_path() . '/upload/property/';
                    $image->move($imagePath , $imageName);
                    $data[] ='/upload/property/'.$imageName;    
                }
                $property->images = json_encode($data);
            }
            $property->entry_date = Carbon::now();
            $property->save();

            if($property){
                return response()->json([
                    'message' => 'property added successfully',
                    'status' => 200,
                    'success' => true
                ]);
            }else{
                return response()->json([
                    'message' => 'something went wrong',
                    'status' => 400,
                    'success' => false
                ]);
            }
            
        }
    }

    public function getallproperty(){
        $property = Property::with('review','subcategory')->where('status','1')->OrderBy('created_at','desc')->get();
        
        if(count($property)>0){
            return response()->json([
                'message'=>'properties retrieved Successfully',
                'status'=>200,
                'data'=>$property
            ],200);
        } else{
            return response()->json([
                'message'=>'No properties found ',
                'status'=>404
            ],404);
        }
    }



    public function sortproperty(Request $request){
        $property = Property::orderBy('property_price', $request->sort)->get();
        if(count($property)>0){
            return response()->json([
                'message'=>'properties retrieved Successfully',
                'status'=>200,
                'data'=>$property
            ],200);
        } else{
            return response()->json([
                'message'=>'No properties found ',
                'status'=>404
            ],404);
        }
    }


   
    public function getrentproperty(){
        $property = Property::where('rent_buy_status','0')->get();
        if(count($property)>0){
            return response()->json([
                'message'=>' rented properties retrieved Successfully',
                'status'=>200,
                'data'=>$property
            ],200);
        } else{
            return response()->json([
                'message'=>'No properties found ',
                'status'=>404
            ],404);
        }
    }

    public function getbuyproperty(){
        $property = Property::where('rent_buy_status','1')->get();
        if(count($property)>0){
            return response()->json([
                'message'=>'  properties retrieved Successfully',
                'status'=>200,
                'data'=>$property
            ],200);
        } else{
            return response()->json([
                'message'=>'No properties found ',
                'status'=>404
            ],404);
        }
    }

    public function getproperty($id){
        $property = Property::find($id);
        $user = User::where('id',$property->user_id)->first();
        if($property){
            
            $priceRange = [
                $property->property_price * 0.9,
                $property->property_price * 1.1  
            ];
    
            $similarProperty = Property::where('subcategory_id',$property->subcategory_id)
                ->whereBetween('property_price', $priceRange)
                ->where('property_district',$property->property_district)
                ->where('id', '!=', $property->id)
                ->where('status','1') 
                ->take(5) 
                ->get();

            return response()->json([
                'message'=>'property retrieved Successfully',
                'status'=>200,
                'property'=>$property,
                'similarProperty'=>$similarProperty,
                'agent' => $user
            ],200);
        } else {
            return response()->json([
                'message'=>'Property not Found',
                'status'=>404
            ],404);
        }
    }

    
    public function getcommercialproperty(){
        $property = Property::with('subcategory')->where('status','1')->where('category_id','1')->get();
        if(count($property)>0){
            return response()->json([
                'message'=>'Commercial properties retrieved successfully',
                'status'=>200,
                'property'=>$property
            ],200);
        } else{
            return response()->json([
                'message'=>'No properties found ',
                'status'=>200
            ],200);
        }
    }

    public function getresidentialproperty(){
        $property = Property::with('subcategory')->where('status','1')->where('category_id','2')->get();
        if(count($property)>0){
            return response()->json([
                'message'=>'Resedential properties retrieved successfully',
                'status'=>200,
                'property'=>$property
            ],200);
        } else{
            return response()->json([
                'message'=>'No properties found ',
                'status'=>200
            ],200);
        }
    }



    public function searchproperty(Request $request){
        $property = Property::where('property_district','LIKE','%'.$request->search . '%')->get();
        if(count($property)>0){
            return response()->json([
                'message'=>'properties retrieved Successfully',
                'status'=>200,
                'data'=>$property
            ],200);
        } else{
            return response()->json([
                'message'=>'No properties found ',
                'status'=>404
            ],404);
        }
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        if ($property) {
            if ($request->filled('category_id')) {
                $property->category_id = $request->category_id;
            }
            if ($request->filled('subcategory_id')) {
                $property->subcategory_id = $request->subcategory_id;
            }
            if ($request->filled('user_id')) {
                $property->user_id = $request->user_id;
            }
            if ($request->filled('property_name')) {
                $property->property_name = $request->property_name;
            }
            if ($request->filled('property_price')) {
                $property->property_price = $request->property_price;
            }
            if ($request->filled('property_address')) {
                $property->property_address = $request->property_address;
            }
            if ($request->filled('property_district')) {
                $property->property_district = $request->property_district;
            }
            if ($request->filled('property_state')) {
                $property->property_state = $request->property_state;
            }
            if ($request->filled('property_pin')) {
                $property->property_pin = $request->property_pin;
            }
            if ($request->filled('property_long')) {
                $property->property_long = $request->property_long;
            }
            if ($request->filled('property_lat')) {
                $property->property_lat = $request->property_lat;
            }
            if ($request->filled('property_landmark')) {
                $property->property_landmark = $request->property_landmark;
            }
            if ($request->filled('property_facing')) {
                $property->property_facing = $request->property_facing;
            }
            if ($request->filled('owner_name')) {
                $property->owner_name = $request->owner_name;
            }
            if ($request->filled('owner_contact')) {
                $property->owner_contact = $request->owner_contact;
            }
            if ($request->filled('bulidup_area')) {
                $property->bulidup_area = $request->bulidup_area;
            }
            if ($request->filled('security_amt')) {
                $property->security_amt = $request->security_amt;
            }
            if ($request->filled('floor')) {
                $property->floor = $request->floor;
            }
            if ($request->filled('society')) {
                $property->society = $request->society;
            }
            if ($request->filled('no_of_beds')) {
                $property->no_of_beds = $request->no_of_beds;
            }
            if ($request->filled('no_of_kitchen')) {
                $property->no_of_kitchen = $request->no_of_kitchen;
            }
            if ($request->filled('no_of_bathroom')) {
                $property->no_of_bathroom = $request->no_of_bathroom;
            }
            if ($request->filled('car_parking')) {
                $property->car_parking = $request->car_parking;
            }
            if ($request->filled('water')) {
                $property->water = $request->water;
            }
            if ($request->filled('invertor')) {
                $property->invertor = $request->invertor;
            }
            if ($request->filled('security')) {
                $property->security = $request->security;
            }
            if ($request->filled('availability_status')) {
                $property->availability_status = $request->availability_status;
            }
            if ($request->filled('furniture_status')) {
                $property->furniture_status = $request->furniture_status;
            }

            if ($request->filled('buyRentStatus')) {
                $property->rent_buy_status = $request->buyRentStatus;
            }


            if($request->hasfile('images')) {
                $data = [];
                foreach($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imageName = str_replace(' ', '_', $imageName);
                    $imagePath = public_path() . '/upload/property/';
                    $image->move($imagePath , $imageName);
                    $data[] ='/upload/property/'.$imageName;    
                }
                $property->images = json_encode($data);
                
            }

            $property->save();

            return response()->json([
                'message' => 'Property updated successfully',
                'status' => 200,
                'success' => true
            ], 200);

        }
        else{
            return response()->json([
                'message' => 'Property not found ',
                'status' => 400,
                'success' => false
            ], 400);
        } 
    }

    
    public function destroy($id)
    {
        $property = Property::find($id);
      
        if($property){
            $property->delete();
            return response()->json([
                'message' => 'property deleted successfully',
                'status' => 200,
                'success' => true
            ],200);
        }
        else{
            return response()->json([
                'message' => 'property not found',
                'status' => 400,
                'success' => false
            ],400);
            

        }
    }

 
    public function like(Request $request){
        $validator = Validator::make($request->all(),[
            'property_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'error'=> $validator->errors(),
                'status' => 400 ,
                'success' => false
            ]);
        }else{
            
            $like = Like::where('property_id',$request->property_id)->where('user_id',Auth::user()->id)->first();
            if($like){
                $like->delete();
                return response()->json([
                    'message' => 'Property disliked successfully',
                    'status' =>200,
                    'success' =>true
                ],200);
            }else{
                $like_create = Like::create([
                    'property_id' => $request->property_id,
                    'user_id' => Auth::user()->id,
                ]);
                if($like_create){
                    return response()->json([
                        'message' => ' Property liked successfully',
                        'status' =>200,
                        'success' =>true
                    ],200);
                }else{
                    return response()->json([
                        'message' => 'something went wrong',
                        'status' =>400,
                        'success' =>false
                    ],400);
                }
            }
        }
    }

  
    public function userLikedPropertyList(){

        $likeList = Property::whereHas('like',function($query){
            $query->where('user_id',Auth::user()->id);
        })->orderBy("created_at", "desc")->get();
        return response()->json([
            'message' => 'User liked property fetched successfully',
            'status' => 200,
            'success' => true,
            'LikedProperty' => $likeList
        ], 200);
    }

    // @get property based on user current location


    public function findNearestProperties(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'status' => 400,
                'success' => false
            ], 400);
        }else{
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius', 5000); 

            $properties = $this->findNearestPropertiesHelper($latitude, $longitude, $radius);

            return response()->json(
                [
                    "message" => "Properties retrieved succesfully",
                    "status" => 200,
                    "properties" => $properties
                ]
            );

        }
      
    }

    private function findNearestPropertiesHelper($latitude, $longitude, $radius)
    {
        $properties = Property::selectRaw(
            "id, property_name, property_price, property_address, property_district, property_state, property_pin, property_lat, property_long, property_landmark, property_facing, owner_name, owner_contact,bulidup_area,floor,security_amt,society,no_of_beds,no_of_kitchen,no_of_bathroom,car_parking,water,invertor,security,availability_status,furniture_status,status,images,entry_date,reason,rent_buy_status,owner_whatsapp,property_description,
        (6371000 * acos(
            cos(radians(?)) * cos(radians(property_lat)) 
            * cos(radians(property_long) - radians(?)) 
            + sin(radians(?)) * sin(radians(property_lat))
        )) AS distance", 
            [$latitude, $longitude, $latitude]
        )
        ->where('status', '=', 1)
        ->having("distance", "<", $radius)
        ->orderBy("distance", 'asc')
        ->offset(0)
        ->limit(20)
        ->get();

        return $properties;
    }

    public function getAllLocality(){

        $localities = Property::selectRaw('property_district, COUNT(*) as property_count')
                          ->groupBy('property_district')->orderBy('property_count', 'desc')
                          ->limit(10)
                          ->get();


        return response()->json([
            'message' => 'Locality fetched successfully',
            'status' => 200,
            'success' => true,
            'locality' => $localities
        ], 200);

    }

   

    public function getPropertiesByLocality(Request $request){

        $validator = Validator::make($request->all(), [
            'district' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'status' => 400,
                'success' => false
            ], 400);
        }else{
            $locality = $request->input('district');
            $properties = Property::where('property_district', $locality)->get();
            if ($properties->isEmpty()) {
                return response()->json([
                    'message' => 'No properties found in this district',
                    'status' => 200,
                    'success' => true,
                ], 200);
            }
            return response()->json([
                'message' => 'Properties fetched successfully',
                'status' => 200,
                'success' => true,
                'properties' => $properties
            ], 200);
        }
            
        }

    public function getSearchData(Request $request){

        $validator = Validator::make($request->all(), [
            'buyRentStatus' => 'required',
            'category_id' => 'required',
            'address' => 'required',
            'min_price' => 'required',
            'max_price' => 'required',
            'sub_category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 400,
                'success' => false
            ], 400);
        }else{
            $buyRentStatus = $request->input('buyRentStatus');
            $category_id = $request->input('category_id');
            $address = $request->input('address');
            $min_price = $request->input('min_price');
            $max_price = $request->input('max_price');
            $sub_category_id = $request->input('sub_category_id');

            $priceRange = [
                $min_price,
                $max_price  
            ];
            $addressParts = explode(',', $address);
            // $country = trim($addressParts[count($addressParts) - 1]);
            $stateAndPin = explode(' ', trim($addressParts[count($addressParts) - 2]));
            $state = $stateAndPin[0];
            $pin = isset($stateAndPin[1]) ? $stateAndPin[1] : '';
            // dd($state,$pin);
            $district = trim($addressParts[count($addressParts) - 3]);
            // dd($district);
            $localityParts = array_slice($addressParts, 0, count($addressParts) - 3);
            $locality = implode(', ', array_map('trim', $localityParts));
            // dd($locality);

            $properties = Property::where('rent_buy_status', $buyRentStatus)
            ->where('category_id', $category_id)
            ->whereBetween('property_price', $priceRange)
            ->where('subcategory_id', $sub_category_id)
            ->where(function ($query) use ($locality, $district, $state, $pin) {
                $query->where('property_address', 'like', '%' . $locality . '%')
                      ->orWhere('property_district', 'like', '%' . $district . '%')
                      ->orWhere('property_state', 'like', '%' . $state . '%')
                      ->orWhere('property_pin', 'like', '%' . $pin . '%');
            })
            ->distinct()
            ->get();

            if ($properties->isEmpty()) {
                return response()->json([
                    'message' => 'No properties found in this category',
                    'status' => 200,
                    'success' => true,
                ], 200);
            }
            return response()->json([
                'message' => 'Properties fetched successfully',
                'status' => 200,
                'success' => true,
                'properties' => $properties
            ], 200);
        }
    }
   
}