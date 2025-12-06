<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Medico;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('medico.loginMedico');
    }

    public function login(Request $request)
    {
        $request->validate([
            'crm' => 'required|string',
            'senha' => 'required|string',
        ]);

        $medico = Medico::with('usuario')->where('crmMedico', $request->crm)->first();

        if (!$medico || !$medico->usuario) {
            return response()->json(['message' => 'CRM inválido.'], 401);
        }

        if (!Hash::check($request->senha, $medico->usuario->senhaUsuario)) {
            return response()->json(['message' => 'Senha incorreta.'], 401);
        }

        Auth::guard('medico')->login($medico->usuario);

        $request->session()->regenerate();
        
        if ($medico->usuario->statusSenhaUsuario == 1) {
            return response()->json([
                'need_password_change' => true,
                'message' => 'Você precisa alterar sua senha antes de continuar.',

                'new_csrf_token' => csrf_token(),
            ]);
        }

        return response()->json([
            'profile_complete' => true,
            'redirect_url' => route('medico.dashboard'),
        ]);
    }

    public function alterarSenhaPrimeiroLogin(Request $request)
    {
        $request->validate([
            'crm' => 'required|string',
            'nova_senha' => 'required|string|min:8|confirmed',
        ]);

        $medico = Medico::with('usuario')->where('crmMedico', $request->crm)->first();

        if (!$medico || !$medico->usuario) {
            return response()->json(['message' => 'Médico não encontrado.'], 404);
        }

        $usuario = $medico->usuario;
        $usuario->senhaUsuario = Hash::make($request->nova_senha);
        $usuario->statusSenhaUsuario = 0;
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso!',
            'redirect_url' => route('medico.dashboard'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('medico')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}