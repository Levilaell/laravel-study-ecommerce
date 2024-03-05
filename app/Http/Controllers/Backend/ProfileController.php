<?php

namespace App\Http\Controllers\backend;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;

class ProfileController extends Controller
{
    public function index() {
        return view('admin.profile.index');
    }

    public function updateProfile(Request $request) {
        $request->validate([
            'name' => ['required', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,'.Auth::user()->id],
            'image' => ['image', 'max:2048']
        ]);

        $user = Auth::user();

        if ($request->hasFile('image')) {
            if (File::exists(public_path($user->image))) {
                File::delete(public_path($user->image));
            }
            $image = $request->image;
            $imageName = rand().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads'), $imageName);

            $path = '/uploads/'.$imageName;

            $user->image = $path;
        }

        
        $user->name = $request->name;
        $user->save();
        
        return redirect()->back();

    }

    /** Update Password */

    public function updatePassword(Request $request) {

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', 'min:8']
        ]);

        $request->user()->update([
            'password' => bcrypt($request->new_password)
        ]);

        return redirect()->back();

    }
}
