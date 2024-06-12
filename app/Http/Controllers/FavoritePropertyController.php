<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavoritePropertyController extends Controller
{
    public function addfavorite(Request $request){
      
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
            
            $favorite = Favorite::where('property_id',$request->property_id)->where('user_id',Auth::user()->id)->first();
            if($favorite){
                $favorite->delete();
                return response()->json([
                    'message' => 'Property remove from wishlist',
                    'status' =>200,
                    'success' =>true
                ],200);
            }else{
                $favorite_create = Favorite::create([
                    'property_id' => $request->property_id,
                    'user_id' => Auth::user()->id,
                ]);
                if($favorite_create){
                    return response()->json([
                        'message' => 'Property added to wishlist successfully',
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

    // public function getAllFavorite(){

    //     $favorite = Favorite::with('property')->where('user_id',Auth::user()->id)->get();

    //     return response()->json([
    //         'message' => 'Favorite property fetched successfully',
    //         'status' => 200,
    //         'success' => true,
    //         'favorite' => $favorite
    //     ], 200);

    // }

    public function getAllFavorite(){

        $favorite = Property::whereHas('favorite',function($query){
            $query->where('user_id',Auth::user()->id);
        })->orderBy("created_at", "desc")->get();
        return response()->json([
            'message' => 'Wishlist property fetched successfully',
            'status' => 200,
            'success' => true,
            'wishlist' => $favorite
        ], 200);
    }
}
