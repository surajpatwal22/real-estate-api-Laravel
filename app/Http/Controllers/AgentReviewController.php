<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\AgentCall;
use App\Models\AgentReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AgentReviewController extends Controller
{
    public function addReview(Request $request){

      
        $validator = Validator::make($request->all(),[
            'agent_id' => 'required',
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
            $review = new AgentReview();
            $review->agent_id = $request->agent_id;
            $review->review = $request->review;
            $review->rating = $request->rating;
            $review->user_id = Auth::user()->id;
            $review->entry_date = Carbon::now();
            $review->status = 1;
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
        $review = AgentReview::find($id);
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
        $review = AgentReview::find($id);
        $review->delete();
        return response()->json([
            'message' => 'Review deleted successfully',
            'status' => 200,
            'success' => true
        ],200);
    }

    //getAgentReview 

    public function getAgentReview($id){

        $review = AgentReview::with('user')->where('agent_id',$id)->get();
        return response()->json([
            'review' => $review,
            'status' => 200,
            'success' => true
        ],200);

    }

    public function getTopAgents(){

        $topAgents = AgentReview::select('agent_id', DB::raw('AVG(rating) as avg_rating'))
                     ->groupBy('agent_id')
                     ->havingRaw('COUNT(*) >= 3')
                     ->orderByDesc('avg_rating')
                     ->with('agent')
                     ->get();

        return response()->json([
            'topAgents' => $topAgents,
            'status' => 200,
            'success' => true
        ],200);
        
    }

    public function storeCallData(Request $request){

        $validator = Validator::make($request->all(),[
            'agent_id' => 'required',
            'user_id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'error'=> $validator->errors(),
                'status' => 400,
                'success' => false
            ],400);
        }else{
            $call = AgentCall::create([
                'agent_id' => $request->agent_id,
                'user_id' => $request->user_id,
                'entry_date' => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Call detail added successfully',
                'call' => $call,
                'status' => 200,
                'success' => true
            ],200);
        }
    }
}
