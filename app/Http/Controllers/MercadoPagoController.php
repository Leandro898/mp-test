<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MercadoPagoController extends Controller
{
    public function redirectToMP()
    {
        $clientId = env('MP_CLIENT_ID');
        $redirectUri = urlencode(env('MP_REDIRECT_URI'));

        $url = "https://auth.mercadopago.com.ar/authorization?client_id={$clientId}&response_type=code&platform_id=mp&redirect_uri={$redirectUri}";

        return redirect($url);
    }

    public function handleCallback(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return "No se recibió el código de autorización.";
        }

        $response = Http::asForm()->post('https://api.mercadopago.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('MP_CLIENT_ID'),
            'client_secret' => env('MP_CLIENT_SECRET'),
            'code' => $code,
            'redirect_uri' => env('MP_REDIRECT_URI'),
        ]);

        if (!$response->successful()) {
            return "Error al obtener access_token: " . $response->body();
        }

        $data = $response->json();

        // 💾 Guardamos en base de datos del usuario autenticado
        $user = auth()->user();
        $user->mp_access_token = $data['access_token'];
        $user->mp_refresh_token = $data['refresh_token'];
        $user->mp_user_id = $data['user_id'];
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Cuenta de Mercado Pago vinculada correctamente.');

    }

    public function testPayment()
    {
        $accessToken = auth()->user()->mp_access_token;

        if (!$accessToken) {
            return "Este usuario no tiene un token vinculado.";
        }

        $response = Http::withToken($accessToken)->get('https://api.mercadopago.com/users/me');

        return $response->json();
    }

    public function unlinkMPAccount()
    {
        $user = auth()->user();

        $user->mp_access_token = null;
        $user->mp_refresh_token = null;
        $user->mp_user_id = null;
        $user->save();

        return redirect()->back()->with('success', 'Cuenta de Mercado Pago desvinculada correctamente.');
    }

}
