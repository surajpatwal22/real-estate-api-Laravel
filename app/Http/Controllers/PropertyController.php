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
            'user_id' => 'required',
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
            'status' => 'required',
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
            $property = new Property();
            $property->category_id = $request->category_id;
            $property->subcategory_id = $request->subcategory_id;
            $property->user_id = $request->user_id;
            $property->property_name = $request->property_name;
            $property->property_price = $request->property_price;
            $property->property_address = $request->property_address;
            $property->property_district = $request->property_district;
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
            $property->status = 1;
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
        $property = Property::all();
        
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

    // @get authorised users liked property list 
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
   
}