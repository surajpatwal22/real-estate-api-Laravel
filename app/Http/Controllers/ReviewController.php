<?php


namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
   

    public function addReview(Request $request){

      
        $validator = Validator::make($request->all(),[
            'property_id' => 'required',
            'review' => 'required',
            'rating' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'error'=> $validator->errors(),
                'status' => 400,
                'success' => false
            ],400);
        }
        else{
            $review = new Review();
            $review->property_id = $request->property_id;
            $review->review = $request->review;
            $review->rating = $request->rating;
            $review->user_id = Auth::user()->id;
            $review->created_date = Carbon::now();
            $data = [];
            if($request->hasfile('images')) {
                foreach($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imageName = str_replace(' ', '_', $imageName);
                    $imagePath = public_path() . '/upload/review/';
                    $image->move($imagePath , $imageName);
                    $data[] ='/upload/review/'.$imageName;    
                }
                $review->image = json_encode($data, JSON_UNESCAPED_SLASHES);
            }
            $review->save();
            $user = User::find($review->user_id);

            return response()->json([
                'review' => $review,
                'status' => 200,
                'success' => true,
                'user' => $user
            ],200);
        }

    }

    public function updateReview(Request $request ,$id){
        $review = Review::find($id);
        if ($review){
            if ($request->filled('property_id')) {
                $review->property_id = $request->property_id;
            }
            if ($request->filled('review')) {
                $review->review = $request->review;
            }
            if ($request->filled('rating')) {
                $review->rating = $request->rating;
            }
            if ($request->filled('user_id')) {
                $review->user_id = $request->user_id;
            }
            
            if ($request->hasfile('images')) {
                $data = [];
                foreach($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imageName = str_replace(' ', '_', $imageName);
                    $imagePath = public_path() . '/upload/review/';
                    $image->move($imagePath , $imageName);
                    $data[] ='/upload/review/'.$imageName;    
                }
                $review->image = json_encode($data, JSON_UNESCAPED_SLASHES);
            }
            $review->save();
            return response()->json([
                'message' => 'Review updated successfully',
                'review' => $review,
                'status' => 200,
                'success' => true
            ],200);


        }
    }

   
    public function destroy($id){
        $review = Review::find($id);
        $review->delete();
        return response()->json([
            'message' => 'Review deleted successfully',
            'status' => 200,
            'success' => true
        ],200);
    }
}
