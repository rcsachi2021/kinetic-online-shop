<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::latest();
        if(!empty($request->get('key')))
        {
            $users = $users->where('name', 'LIKE', '%'.$request->get('key').'%');
            $users = $users->orWhere('email', 'LIKE', '%'.$request->get('key').'%');
        }
        $users = $users->paginate(10);

        return view('admin.users.list', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|min:5'
        ]);

        if($validator->passes()){

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = $request->password;
            $user->status = $request->status;
            $user->save();

            session()->flash('success', 'User saved successfully');
            return response()->json([
                'status' => true,
                'message' => 'User saved successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        if($user == null){
            return redirect()->route('users.index')->with('error', 'User not found!');
        }
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if($user == null)
        {
            session()->flash('error', 'User not found');
            return response()->json([
                'status' => true,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id.',id',
            'phone' => 'required',
            'status' => 'required'
        ]);

        if($validator->passes()){
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            if($request->password != null){
                $user->password = Hash::make($request->password);
            }
            $user->status = $request->status;
            $user->save();
            session()->flash('success','User updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'User updated successfully'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if($user == ''){
            session()->flash('error', 'User not found');
            return response()->json([
                'status' => true
            ]);
        }
        $user->delete();
        session()->flash('success', 'User deleted successfully');
        return response()->json([
            'status' => true
        ]);
    }
}
