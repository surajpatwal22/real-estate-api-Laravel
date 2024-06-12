<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function SignUp(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string', 
         ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ],400);
        } else{
            $user = User::create([
                'name' =>  $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->password),
                'status' => 1 ,
                'is_admin' => 0,
            ]);
            if ($user) {
                return response()->json([
                    'message' => 'Account created successfully',
                    'status' => 201,
                    'success' => true
                ]);
            } else {
                return response()->json([
                    'message' => 'something went wrong',
                    'status' => 400,
                    'success' => false
                ]);
            }
            
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
            'device_id'=>'required'
        ]);
       
        if($validator->fails()){
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ],400);
        }else{
            $user = User::where('email',$request->email)->first();
            if ($user) {
                $check = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
                // return $check;
                if ($check == 1) {
                    $token = $user->createToken('Personal Access Token')->plainTextToken;
                    $user->device_id = $request->device_id;
                    $user->save();
                    return response()->json([

                        'message' => 'Login successfull',
                        'token' => $token,
                        'status' => 200,
                        'success' => true,
                        'data' => $user
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'credentials not matched',
                        'status' => 400,
                        'success' => false
                    ], 400);
                }
        
            }else{
                return response()->json([
                    'message' => 'Email Not found',
                    'status' => 404,
                    'success' => false
                ],404);
            }
        }
    }

    public function getProfile()
{
    
    if (!Auth::check()) {
        return response()->json([
            'message' => 'Unauthorized',
            'status' => 401,
            'success' => false
        ], 401);
    }

    return response()->json([
        'user' => Auth::user(),
        'status' => 200,
        'success' => true
    ], 200);
}

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user) {
            $validator = Validator::make($request->all(), [
                'email' => 'email',
                'contact' => 'min:10|max:10'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors(),
                    'status' => 400,
                    'success' => false
                ]);
            } else {
                if ($request->image) {
                    try {
                        $file = $request->file('image');
                        $imageName = time() . '.' . $file->getClientOriginalName();
                        $imageName = str_replace(' ', '_', $imageName);
                        $imagePath = public_path() . '/upload/user';
                        $file->move($imagePath, $imageName);
                        $user->image = '/upload/user/' . $imageName;
                      
                    } catch (Exception $e) {
                        return $e;
                    }
                }

                if ($request->email) {
                    $user->email = $request->email;
                }
                if ($request->contact) {
                    $user->contact = $request->contact;
                }
                if ($request->name) {
                    $user->name = $request->name;
                }
                if ($request->bio) {
                    $user->bio = $request->bio;
                }
                if ($request->address) {
                    $user->address = $request->address;
                }
                if ($request->state) {
                    $user->state = $request->state;
                }
                if ($request->pin) {
                    $user->pin = $request->pin;
                }
                if ($request->district) {
                    $user->district = $request->district;
                }

                $user->save();

                return response()->json([
                    'message' => 'updated successfully',
                    'status' => 200,
                    'success' => true
                ]);
            }
        } else {
            return response()->json([
                'message' => 'user not found',
                'status' => 404,
                'success' => false
            ]);
        }
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json([
            'message' => 'Successfully logged out',
            'status' => 200,
            'success' => true
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email Not found',
                'status' => 404,
                'success' => false
            ]);
        }

        $otp = rand(1000, 9999);

        Otp::where('user_id', $user->id)->delete();

        $create = Otp::create([
            'otp' => $otp,
            'user_id' => $user->id,
            'status' => 1
        ]);

        if ($create) {
            try {
                // Mail::send('mail.verificationMail', ['otp' => $otp, 'name' => $user->name], function ($m) use ($user) {
                //     $m->to($user->email, $user->name)->subject("Verification otp");
                // });
                return response()->json([
                    'message' => 'Otp sent to mail ,valid upto 5 minutes',
                    'otp' => $otp,
                    'status' => 200,
                    'success' => true
                ]);
            } catch (Exception $e) {
                return $e;
            }
        } else {
            return response()->json([
                'message' => 'something went wrong,Please try again later',
                'status' => 400,
                'success' => false
            ]);
        }
    }
    
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
            'device_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ]);
        } else {
            $user = User::where('email', $request->email)->first();
            if ($user) {

                $expiryTime = Carbon::now()->subMinutes(5);
                Otp::where('created_at', '<=', $expiryTime)->delete(); 

                $check = Otp::where(['user_id' => $user->id, 'otp' => $request->otp])->first();
                if ($check) {
                    $token = $user->createToken('Personal Access Token')->plainTextToken;
                    $user->device_id = $request->device_id;
                    $user->save();

                    return response()->json([

                        'message' => 'otp verified',
                        'token' => $token,
                        'status' => 200,
                        'success' => true
                    ]);
                } else {
                    return response()->json([
                        'message' => 'credentials not matched or otp expired',
                        'status' => 400,
                        'success' => false
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Email Not found',
                    'status' => 404,
                    'success' => false
                ]);
            }
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ]);
        } else {
            $user = User::find(Auth::user()->id);
            if ($user) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([
                    'message' => 'password updated successfully',
                    'status' => 200,
                    'success' => true
                ]);
            } else {
                return response()->json([
                    'message' => 'User Token not found',
                    'status' => 404,
                    'success' => false
                ]);
            }
        }
    }

    
    
}
