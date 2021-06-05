<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\File;
use nusoap_client;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Login username to be used by the controller.
     *
     * @var string
     */
    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        $this->username = $this->findUsername();
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        $usuario = (strstr($request->email, '@', true) . "\n");
        $dominio = (str_replace("@", "", strstr($usuario, '@')) . "\n");

        $params = array(
            "id" => $usuario,
            "clave" => $request->password,
        );

        $client = new nusoap_client('http://172.25.16.18/bus/webservice/ws.php?wsdl');
        $response = $client->call('autentifica_ldap', $params);


        if ($response['resp']==0){
            return redirect()->back()->withErrors($response['mensaje']);
        }

        $user = User::where('email', '=', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'username' => $usuario,
                'name' => $response['nombre'],
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
        }

        Auth::login($user);

    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }


    /**
     * Get username property.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Redirecciona al usuario al proveedor de autenticación.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider (string $driver) {
        return Socialite::driver($driver)->redirect();
    }

    /**
     * Se obtiene la informacion de usuario del proveedore de autenticación despues de aceptar.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(string $driver)
    {
        if( ! request()->has('code') || request()->has('denied')) {
            flash( __("Inicio de sesión cancelado"))->error()->important();
            return redirect('login');
        }

        $socialUser = Socialite::driver($driver)->user();

//        dd($socialUser);
        $userLocal = User::whereEmail($socialUser->getEmail())->first();



        //si no existe el usuario localmente se debe crear
        if (is_null($userLocal)){

            try {
                DB::beginTransaction();

                $userLocal = User::create([
                    "name" => $socialUser->getName(),
                    "username" => $socialUser->getNickname(),
                    "email" => $socialUser->getEmail(),
                    "provider" => $driver,
                    "provider_uid" => $socialUser->getId(),
                    //"avatar" => $fileName
                ]);

                $avatar= $driver=='facebook' ? $socialUser->avatar_original : $socialUser->getAvatar();

                $userLocal->addMediaFromUrl($avatar)->toMediaCollection('avatars');

                $userLocal->syncRoles([Role::TESTER]);
                $userLocal->options()->sync(Option::pluck('id')->toArray());

            } catch (\Exception $exception) {
                DB::rollBack();

                throw new \Exception($exception);
            }


            DB::commit();

        }


        auth()->loginUsingId($userLocal->id);

        return redirect(route('home'));


    }

}
