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

        $data = $response->json();

        if ($response->successful()) {
            // Guardás temporalmente el access_token para pruebas
            session(['mp_token' => $data['access_token'], 'mp_user_id' => $data['user_id']]);
            return redirect('/test-payment');
        }

        return "Error al obtener access_token: " . $response->body();
    }

    public function testPayment()
    {
        $accessToken = session('mp_token');

        if (!$accessToken) {
            return "No hay token en sesión.";
        }

        $response = Http::withToken($accessToken)->get('https://api.mercadopago.com/users/me');

        return $response->json();
    }
}

