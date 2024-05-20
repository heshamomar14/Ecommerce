<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestValueResolver;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users=User::latest();

        //search in users
        if (!empty($request->get('keyword'))){
            $users=$users->where('name','like','%'.$request->get('keyword').'%');
            $users=$users->where('email','like','%'.$request->get('keyword').'%');
        }
        $users=$users->paginate(10);

        return view('admin.users.index',[
            'users'=>$users
        ]);
    }
    public function create(Request $request)
    {
        return view('admin.users.create');
    }
    public function store(Request $request)
    {

        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:5'
        ]);
        if ($validator->passes()){
            $user= new User;
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password= Hash::make($request->password);
            $user->phone=$request->phone;
            $user->status=$request->status;
            $user->save();
        session()->flash('success','user added successfully');
            return response()->json([
                'status'=>true,
             'message'=>'user added successfully'
            ]);
        }
        else{
            return response()->json([
               'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function edit(Request $request,$userId)
    {
        $user=User::find($userId);
      if(!empty($user)){
          return view('admin.users.edit',[
              'user'=>$user
          ]);

      }
    }
    public function update(Request $request,$userId)
    {
        $user=User::find($userId);
        if (empty($user)){
            return response()->json([
                'status' => false,
                'notfound' => true,
                'message' => 'user not found'
            ]);

        }
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.$userId,',id',

        ]);
        if ($validator->passes()){
            $user->name=$request->name;
            $user->email=$request->email;
            if ($request->password !='')
            {$user->password= Hash::make($request->password);}
            $user->phone=$request->phone;
            $user->status=$request->status;
            $user->save();
            session()->flash('success','user updated successfully');
            return response()->json([
                'status'=>true,
                'message'=>'user updated successfully'
            ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function destroy($id, Request $request)
    {

        $user=User::find($id);

        if (empty($user)) {
            $request->session()->flash('error', 'user not found ');
            return response()->json(['status' => true, 'message' => 'user not found']);
//                return redirect(route('categories.index'));
        }
        $user->delete();
        session()->flash('success', 'user deleted successfuly');
        return response()->json(['status' => true, 'message' => 'user deleted successfuly']);

    }
}

