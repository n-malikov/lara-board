<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/cabinet';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'verify_token' => \Illuminate\Support\Str::random(),
            'status' => User::STATUS_WAIT,
        ]);
    }


    /**
     * Handle a registration request for the application.
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->create($request->all());
        //Mail::to($user->email)->send(new \App\Mail\VerifyMail($user));
        event(new Registered($user));

        //$this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }


    /**
     * The user has been registered.
     */
    protected function registered(Request $request, $user)
    {
        //Auth::logout();
        $host = \Illuminate\Support\Facades\Request::root();
        return redirect()
            ->route('login')
            //->with('status', 'Check your email and click on the link to verify.');
            ->with('status', "Check your email and click on the link to verify. DEV MODE: {$host}/verify/" . $user->verify_token);
    }


    public function verify ($token)
    {
        if ( !$user = User::where('verify_token', $token)->first() )
            return redirect()->route('login')
                ->with('error', 'Sorry your link cannot be identified.');

        $user->status = User::STATUS_ACTIVE;
        $user->verify_token = null;
        $user->save();

        return redirect()->route('login')
            ->with('success', 'Your e-mail is verified. You can now login.');
    }

}
