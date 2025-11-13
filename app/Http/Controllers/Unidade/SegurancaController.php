<?php

namespace App\Http\Controllers\Unidade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Unidade; // Importar o Model Unidade

class SegurancaController extends Controller 
{
    
    public function showAlterarSenhaForm()
    {
        return view('unidade.seguranca');
    }

    public function atualizarPerfil(Request $request)
    {
        // 1. Pega a Unidade logada (isto está correto)
        $unidade = Auth::guard('unidade')->user();

        $validatedData = $request->validate([
            'nomeUnidade' => 'required|string|max:255',
            'emailUnidade' => 'required|email|max:255|unique:tbUnidade,emailUnidade,' . $unidade->idUnidadePK . ',idUnidadePK', // Correção na validação unique
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Atualiza os dados diretos
        $unidade->nomeUnidade = $validatedData['nomeUnidade'];
        $unidade->emailUnidade = $validatedData['emailUnidade'];

        // 3. Lógica da Foto (CORRIGIDA)
        if ($request->hasFile('foto')) {
            
            // Apaga a foto antiga (se existir) da tbUnidade
            if ($unidade->foto && Storage::disk('public')->exists($unidade->foto)) {
                Storage::disk('public')->delete($unidade->foto);
            }

            // Salva a nova foto e atualiza o campo 'foto' na tbUnidade
            // (Certifique-se de ter rodado 'php artisan storage:link')
            $fotoPath = $request->file('foto')->store('fotos_unidade', 'public');
            $unidade->foto = $fotoPath;
        }

        $unidade->save(); // Salva todas as alterações

        return redirect()->route('unidade.perfil')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function alterarSenha(Request $request)
    {
        $request->validate([
            'senha_atual' => 'required',
            'nova_senha' => 'required|min:8|confirmed',
        ], [
            'nova_senha.confirmed' => 'A confirmação da senha não corresponde.',
            'nova_senha.min' => 'A nova senha precisa ter no mínimo 8 caracteres.',
        ]);

        // =============================================
        // --- AQUI ESTÁ A CORREÇÃO ---
        // =============================================
        
        // 1. Pega a Unidade logada (que é o usuário)
        $unidade = Auth::guard('unidade')->user();

        // 2. Verifica a senha atual direto na tbUnidade (coluna 'senhaUnidade')
        if (!Hash::check($request->senha_atual, $unidade->senhaUnidade)) {
            return back()->withErrors(['senha_atual' => 'Senha atual incorreta.']);
        }

        // 3. Salva a nova senha direto na tbUnidade
        $unidade->senhaUnidade = Hash::make($request->nova_senha);
        $unidade->save();
        
        // =============================================
        
        return back()->with('success', 'Senha alterada com sucesso!');
    }
}