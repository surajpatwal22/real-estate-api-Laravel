<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    public function index(){
        $agents = Agent::where('status','1')->paginate(10);
        return response()->json([
            'message'=> 'agents retrieve successfully',
            'status'=>200,
            'data' =>$agents
        ],200)
    }
    public function addAgent(Request $request){
        $users = User::all();
        return view('admin.agents.addagent',compact('users'));
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id',
            'email' => 'required',
            'contact_no' => 'required',
            'address' => 'required',
            'state' => 'required',
            'bio' => 'required',
            'availability_status' => 'required',
            'status' => 'required',
            'image' => 'required',
            'aadhar_img' => 'required',
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }else{

            $user = User::find($request->user_id);
            if ($request->image) {
                $file = $request->image;
                $imageName1 = $file->getClientOriginalName();
                $imageName1 = str_replace(' ', '_', $imageName1);
                $imagePath = public_path() . '/public/images/agents/image/';
                $file->move($imagePath, $imageName1);
            } 
            if ($request->aadhar_img) {
                $file = $request->aadhar_img;
                $imageName = $file->getClientOriginalName();
                $imageName = str_replace(' ', '_', $imageName);
                $imagePath = public_path() . '/public/images/agents/image/';
                $file->move($imagePath, $imageName);
            } 
            $agent = new Agent();
            $agent->user_id = $user->id;
            $agent->name = $user->name;
            $agent->email = $request->email;
            $agent->contact_no = $request->contact_no;
            $agent->address = $request->address;
            $agent->state = $request->state;
            $agent->bio = $request->bio;
            $agent->availability = $request->availability_status;
            $agent->agency_name = $request->agency_name;
            $agent->license_number = $request->license_number;
            $agent->experience_years = $request->experience_years;
            $agent->status = $request->status;
            $agent->profile = 'public/storage/agents/image/' . $imageName1;
            $agent->aadhar_img = 'public/storage/agents/image/' . $imageName;
            $agent->entry_date = Carbon::now();
            $agent->entry_by = Auth::user()->id;
            $agent->save();

            if($agent){
                return redirect()->route('agents')->with('status', 'agent added successfully');
                return response()->json([
                    "success" => true,
                    "message" => "Agent created successfully."
                ])
            }else{
                return response()->json([
                    "success" => false,
                    "message" => "Something went wrong. Please try again later.",
                    "status" =>400
                ]);
            }
            
        }
    }

}
