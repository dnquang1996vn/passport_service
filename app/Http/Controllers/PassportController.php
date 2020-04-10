<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PassportController extends Controller
{
    public function login(Request $request)
    {
        $response = $this->ssoOauth($request);

        if ($response->status() !== 200) {
            return response('Unauthorized', 401);
        }


        $user = User::firstOrCreate([
            'email' => $request->input('email')
        ]);

        return [
            'user'         => $user,
            'assess_token' => $user->createToken('token')->plainTextToken
        ];
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);
        //TODO check email is unique in SSO db as well
        $this->findOrCreateSsoUser($request);

        $user = User::Create([
            'email' => $request->input('email')
        ]);

        return [
            'user'         => $user,
            'assess_token' => $user->createToken('token')->plainTextToken
        ];
    }

    private function ssoOauth(Request $request)
    {
        return Http::post('http://127.0.0.1:8000/oauth/token', [
            'grant_type'    => 'password',
            'client_id'     => 2,
            'client_secret' => 'ZBMAdoyOSAXwgxvV1kY2hIv6vyUIObPNPizkanfw',
            'username'      => $request->input('email'),
            'password'      => $request->input('password'),
            'scope'         => null
        ]);
    }

    private function findOrCreateSsoUser(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8000/oauth/token', [
            'grant_type'    => 'client_credentials',
            'client_id'     => 10,
            'client_secret' => 'vam7x5dncNFPiPhtqlRnUf7swQR6YsfleoPzXuve',
        ]);

        $systemToken = $response->json()['access_token'];

        $response = Http::withToken($systemToken)->post('http://127.0.0.1:8000/api/user', [
            'email'    => $request->input('email'),
            'password' => $request->input('password')
        ]);
    }
}
