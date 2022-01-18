<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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
    public function index()
    {
        $user=User::whereRoleIs('admin')->when($request->search,function($query) use ($request){

                    return $query->where('first_name','like','%'. $request->search .'%')
                            ->onWhere('last_name','like','%'. $request->search .'%');

            });

        })->latest()->paginate(2);
        
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
            'email'=>'requierd',
            'password'=>'requierd|confirmed',
        ]);

        $request_data=$request->except(['password','password_confirmation','permissions']);
        $request_data['password']=bcrypt($request->password);

        $user=User::create($request_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);

        sesion()->flash('success',('site.added.successfully'));

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
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name'=>'requierd',
            'last_name'=>'requierd',
            'email'=>'requierd',
        ]);

        $request_data=$request->except(['permissions']);
        $user->update($request_data);

        $user->syncPermissions($request->permissions);

        sesion()->flash('success',('site.updated.successfully'));

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
        $user->delete();


        sesion()->flash('success',('site.deleted.successfully'));

        return redirect()->route('dashboard.users.index');

    
    }
}
