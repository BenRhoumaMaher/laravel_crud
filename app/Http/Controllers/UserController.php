<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => ['required','min:3','max:20',Rule::unique('users', 'username')],
            'email' => ['required','email',Rule::unique('users', 'email')],
            'password' => ['required','min:8','confirmed']
        ]);
        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Thank you for creating an account');
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required',
        ]);
        if(auth()->attempt([
        'username' => $incomingFields['loginusername'],
        'password' => $incomingFields['loginpassword'],
        ])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have logged in.');
        } else {
            return redirect('/')->with('failure', 'Invalid credentials.');
        }
    }
    public function showCorrectHomepage()
    {
        if(auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', 'You have logged out.');
    }

    public function profile(User $user)
    {
        return view('profile-posts',[
            'avatar' => $user->avatar,
            'username' => $user->username,
            'posts' => $user->posts()->latest()->get(),
            'postCount' => $user->posts()->count()
        ]);
    }
    public function showAvatarForm(){
        return view('/avatar-form');
    }
    public function storeAvatar(Request $request){
        $request->validate([
            'avatar' => 'required|image|max:6000'
        ]);
        $user  = auth()->user();
        $filename = $user->id . '-' . uniqid() . '.jpg';
        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        Storage::put('public/avatars/'.$filename,$imgData);

        $old = $user->avatar;
        $user->avatar = $filename;
        $user->save();

        if($old != "/fallback-avatar.jpg"){
            Storage::delete(str_replace("/storage/","public/",$old));
        }
        return back()->with('success','a new avatar has been added !!!');
    }
}