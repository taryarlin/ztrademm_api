<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Hash;
use DB;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    //

    public function index(){
    //   $all_staffs = User::with('roles')->whereNotIn('name', ['User', 'Customer',])->get();
    $all_staffs = User::with('roles')->whereHas("roles", function($q){ $q->where("name", "!=", "User"); })->get(); 
      return $all_staffs;
    }
    
    public function getCustomers(){
        $customers = User::role('User')->get(); 
        return $customers;
    }
    
    public function getUserCount(){
        $userCount= User::role('User')->count();
        $staffCount=User::whereHas("roles", function($q){ $q->where("name", "!=", "User"); })->count();
        
        return response()->json([
           "user" => $userCount,
           "staffs" => $staffCount
        ], 200);

    }
    
    public function getStaffCount(){
        $staffCount= User::whereHas("roles", function($q){ $q->where("name", "!=", "User"); })->count();
        return $staffCount;
    }

    public function getStaffs(){
    //   $all_users_with_all_their_roles = User::role('User')->get(); Get Users
//   $all_users_with_all_their_roles = Role::whereNotIn('name', ['User'])->get();
           $all_users_with_all_their_roles=User::whereHas("roles", function($q){ $q->where("name", "!=", "User"); })->get(); // Get Staffs
            
            return $all_users_with_all_their_roles;
                    
              
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $latestUser = User::latest()->first();
        $latestUser->update([
            'verification_code' => sha1(time()),
            'is_verified' => 1
        ]);
        // MailController::sendSignupEmail($user->name, $user->email, latestUser);
        $user->assignRole($request->input('roles'));

        return response()->json([
            "message"=> "Successfully Created"
        ], 201);
    }


    public function update(Request $request,$id)
    {
        // $this->validate($request, [
        //     'name' => 'required',
        //     'email' => 'required|email|unique:users,email',
        //     'password' => 'required|same:confirm-password',
        //     'roles' => 'required'
        // ]);
        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        return response()->json([
            "message"=> "Successfully Created"
        ], 201);
    }

    public function destroy($id)
   {
       User::find($id)->delete();
       return response()->json([
           "message"=> "Successfully Deleted"
       ], 201);
   }
}
