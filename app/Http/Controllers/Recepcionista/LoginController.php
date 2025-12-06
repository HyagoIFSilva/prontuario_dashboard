<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostra o formulário de login.
     */
    public function showLoginForm()
    {
        return view('recepcionista.loginRecepcionista');
    }

    /**
     * Processa a tentativa de login (chamado pelo AJAX).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ]);

   
        $credentials = [
            'emailRecepcionista' => $request->email,
            'password' => $request->senha
        ];

        if (Auth::guard('recepcionista')->attempt($credentials)) {
            
            $request->session()->regenerate();


            return response()->json([
                'message' => 'Login bem-sucedido!',
                'redirect_url' => route('recepcionista.dashboard') 
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['As credenciais fornecidas estão incorretas.'],
        ]);
    }


       public function logout(Request $request)
    {
        Auth::guard('recepcionista')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logout realizado com sucesso.');
    }
}
