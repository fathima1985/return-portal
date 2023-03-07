<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InfoUserController extends Controller
{


    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function create()
    {
        return view('dashboard/user-management');
    }


    public function DeleteUser(Request $request)
    {
        $users      = User::all();
        if(count($users) > 1){
            $_id = $request->input('id');
            $_user = User::where('id', $_id)->delete();
            return redirect('/users')->with('success','User Deleted successfully');
        }
        return redirect('/users')->with('danger','User cant be deleted');
    }

    public function EditUser(Request $request){


        $_id = $request->input('id');
        if(!$_id)
            $_id = $this->user->id;

        $user = User::where('id', $_id)->get()->first();
        
        if(empty($user))
            return redirect('/users')->with('danger','Invalid User');
        else
            return view('dashboard/edit-profile',compact('user'));  

      // return redirect('/user-profile')->with('success','Profile updated successfully');

    }
    
    public function createNewUser(Request $request){
        $attributes = request()->validate([
            'name'      => ['required', 'max:50'],
            'email'     => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'phone'     => ['max:50'],
            'location'  => ['max:70'],
            'password'  => 'min:6|required_with:confirm|same:confirm',
            'confirm'   => 'min:6'
        ]);


        $data = [
            'name'      => $attributes['name'],
            'email'     => $attributes['email'],
            'phone'     => $attributes['phone'],
            'location'  => $attributes['location'],
            'password'  => Hash::make($attributes['password']),           
        ];
        User::create($data);
        return redirect('/users')->with('success','User created successfully');

    }

    public function store(Request $request)
    {

        $_id = $request->input('user_id');
        
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore($_id)],
            'phone'     => ['max:50'],
            'location' => ['max:70'],            
        ]);

        $update = [
            'name'      => $attributes['name'],
            'email'     => $attributes['email'],
            'phone'     => $attributes['phone'],
            'location'  => $attributes['location'],                    
        ];

        $password =  $request->input('password');
        if($password != ''){
            $attributes = request()->validate([                                
                'password'  => 'min:6|required_with:confirm|same:confirm',
                'confirm'   => 'min:6'
            ]);
           $update['password']  = Hash::make($password); 
        }        
       
        User::where('id',$_id)->update($update);

        return redirect('/edit-profile/?id='.$_id)->with('success','Profile updated successfully');
    }

    public function createUser(){

        return view('dashboard/user-create');

    }
    public function Users(Request $request){       
        
		/*$offset 	= isset($_GET['page']) ? $_GET['page'] : 0;
		$limit		= 10;
		if($offset > 1){
			$offset = ($offset - 1) * $limit;
		}else{
			$offset = 0;
		}
        $paginate 	= User::all()->paginate(10);		
        $users      = User::all()->offset($offset)->limit($limit)->get();         */
        $users      = User::all();
        return view('dashboard/user-management',compact('users'));  
    }
}
