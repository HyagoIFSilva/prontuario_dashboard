<?php

namespace App\Http\Controllers\Recepcionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Recepcionista;

class RecepcionistaConfiguracaoController extends Controller
{
    public function perfil()
    {
        $recepcionista = Auth::guard('recepcionista')->user();

        if (!$recepcionista) {
            return redirect()->route('recepcionista.login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        return view('recepcionista.perfilRecepcionista', compact('recepcionista'));
    }

    public function atualizarPerfil(Request $request)
{
    $recepcionista = Auth::guard('recepcionista')->user();

    $request->validate([
        'nomeRecepcionista' => 'required|string|max:255',
        'emailRecepcionista' => [
            'required',
            'email',
            'max:255',
            Rule::unique('tbRecepcionista', 'emailRecepcionista')
                ->ignore($recepcionista->idRecepcionistaPK, 'idRecepcionistaPK'),
        ],
    ]);

    $recepcionista->nomeRecepcionista = $request->nomeRecepcionista;
    $recepcionista->emailRecepcionista = $request->emailRecepcionista;

    if ($request->hasFile('foto')) {

        if ($recepcionista->foto && \Storage::exists('public/' . $recepcionista->foto)) {
            \Storage::delete('public/' . $recepcionista->foto);
        }

        $path = $request->file('foto')->store('recepcionistas', 'public');
        $recepcionista->foto = $path;
    }

    $recepcionista->save();

    return redirect()->route('recepcionista.perfil')->with('success', 'Perfil atualizado com sucesso!');
}

    public function showAlterarSenhaForm()
    {
        return view('recepcionista.segurancaRecepcionista');
    }

    public function alterarSenha(Request $request)
    {
        $recepcionista = Auth::guard('recepcionista')->user();

        if (!$recepcionista) {
            return redirect()->route('recepcionista.login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $request->validate([
            'senha_atual' => 'required',
            'nova_senha' => 'required|min:8|confirmed',
        ], [
            'senha_atual.required' => 'A senha atual é obrigatória.',
            'nova_senha.required' => 'A nova senha é obrigatória.',
            'nova_senha.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
            'nova_senha.confirmed' => 'A confirmação da senha não confere.',
        ]);

        if (!Hash::check($request->senha_atual, $recepcionista->senhaRecepcionista)) {
            return back()->withErrors(['senha_atual' => 'Senha atual incorreta.']);
        }

        $recepcionista->senhaRecepcionista = Hash::make($request->nova_senha);
        $recepcionista->save();

        return back()->with('success', 'Senha alterada com sucesso!');
    }
}