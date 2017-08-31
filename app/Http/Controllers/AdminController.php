<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreNewAdmin;
use App\Admin;
use App\User;
use App\Tag;
use App\Instrument;
use App\Style;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = Admin::orderBy('id', 'ASC')->paginate(5);
        $tags = Tag::orderBy('id', 'DES')->get();
        $instruments = Instrument::orderBy('id', 'DES')->get();
        $styles = Style::orderBy('id', 'DES')->get();
        $number_of_members = User::all()->count();

        return view('admin.dashboard')
                        ->with('admins', $admins)
                        ->with('tags', $tags)
                        ->with('instruments', $instruments)
                        ->with('styles', $styles)
                        ->with('number_of_members', $number_of_members);
    }

    public function create()
    {
        return view('admin.register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNewAdmin $request)
    {
        $admin = new Admin($request->all());
        $admin->name = $request->name;
        $admin->email = $request->email;
        if($request->permission == 1)
        {
            $admin->permission = 'higher';
        }
        else
        {
            $admin->permission = 'lower';
        }
        $admin->password = bcrypt($request->password);
        $admin->save();
        return redirect()->route('admin.dashboard');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admin::find($id);
        $admin->delete();
        return redirect()->route('admin.dashboard');
    }

    public function manage_user()
    {
        $users = User::orderBy('id', 'DES')->paginate(25);
        return view('admin.manage')->with('users', $users);
    }

    public function blockuser($id)
    {
        User::where('id', $id)
            ->update([
                'active' => 0
            ]);
        return redirect()->route('admin.manage_user');
    }

    public function unlockuser($id)
    {
        User::where('id', $id)
            ->update([
                'active' => 1
            ]);
        return redirect()->route('admin.manage_user');
    }

    public function nonvisible($id)
    {
        User::where('id', $id)
            ->update([
                'visible' => 0
            ]);
        return redirect()->route('admin.manage_user');
    }

    public function visible($id)
    {
        User::where('id', $id)
            ->update([
                'visible' => 1
            ]);
        return redirect()->route('admin.manage_user');
    }
}
