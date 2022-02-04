<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:read_users'])->only('index');
        $this->middleware(['permission:create_users'])->only('create');
        $this->middleware(['permission:update_users'])->only('edit');
        $this->middleware(['permission:delete_users'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user=User::whereRoleIs('admin')->where(function($q) use ($request){

            return $q->when($request->search,function($query)use($request){

                return $query->where('first_name','like','%'. $request->search .'%')
                ->onWhere('last_name','like','%'. $request->search .'%');

            });

        })->latest()->paginate(5);







        return view('dashboard.users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name'=>'requierd',
            'last_name'=>'requierd',
            'email'=>'requierd|unique:users',
            'image'=>'image',
            'password'=>'requierd|confirmed',
            'permissions'=>'requierd:1',
        ]);

        $request_data=$request->except(['password','password_confirmation','permissions','image']);
        $request_data['password']=bcrypt($request->password);

        if($request->image){

            Image::make($request->image)->resize(300,null,function($constraint){
                $constraint->aspectRatio();
            })->save(public_path('uploads/user_images/'. $request->image->hashName()));

            $request_data['image']=$request->image->hashName();
        }

        $user=User::create($request_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);

        session()->flash('success',('site.added.successfully'));

        return redirect()->route('dashboard.users.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('dashboard.users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name'=>'requierd',
            'last_name'=>'requierd',
            'email'=>['required',Role::unique('users')->ignore($user->id)],
            'image'=>'image',
            'permissions'=>'requierd|min:1',

        ]);

        $request_data=$request->except(['permissions','image']);


        if($request->image){

           if($user->image!='defualt.png'){
            Storage::disk('public_uploads')->delete('/user_images/'.$user->imgae);

           }

           Image::make($request->image)->resize(300,null,function($constraint){
            $constraint->aspectRatio();
        })->save(public_path('uploads/user_images/'. $request->image->hashName()));

        $request_data['image']=$request->image->hashName();

        }



        $user->update($request_data);

        $user->syncPermissions($request->permissions);

        session()->flash('success',('site.updated.successfully'));

        return redirect()->route('dashboard.users.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        if($user->image!='default.png'){

            Storage::disk('public_uploads')->delete('/user_images/'.$user->imgae);
        }
        $user->delete();


        session()->flash('success',('site.deleted.successfully'));

        return redirect()->route('dashboard.users.index');


    }
}
