<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $user = auth()->user();
        return view('Template::user.profile_setting', compact('pageTitle','user','countries'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
        ],[
            'address.required'=>'@lang("Address is required")',
        ]);
        $user = auth()->user();
        if(!$user->dni)
        {
            $request->validate([
                'dni' => 'required|string',
            ],[
                'dni.required'=>'@lang("DNI is required")',
            ]);
            $user->dni = $request->dni;
        }

        $user->country_name = $request->country;
        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->dial_code    = $request->mobile_code;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;
        $user->profile_complete = Status::YES;

        $user->save();
        $notify[] = ['success', '@lang("Profile updated successfully")'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view('Template::user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required','confirmed',$passwordValidation]
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
