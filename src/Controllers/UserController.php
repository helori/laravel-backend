<?php

namespace Helori\LaravelBackend\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Helori\LaravelAdmin\Models\Admin;
use Helori\LaravelAdmin\Requests\Admin as AdminRequest;


class UserController extends Controller
{
    public function read(Request $request)
    {
        $users = Admin::orderBy('name', 'asc')->get();
        return $users;
    }

    public function create(AdminRequest $request)
    {
        $user = new Admin();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return $user;
    }

    public function update(AdminRequest $request, $id)
    {
        $user = Admin::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password){
            $user->password = bcrypt($request->password);
        }
        $user->save();
        return $user;
    }

    public function delete(Request $request, $id)
    {
        $user = Admin::findOrFail($id);
        $user->delete();
    }
}


