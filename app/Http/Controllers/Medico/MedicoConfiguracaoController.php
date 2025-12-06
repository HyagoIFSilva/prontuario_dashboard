<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Medico;
use App\Models\Usuario; 

class MedicoConfiguracaoController extends Controller
{
    public function perfil()
    {
        $usuario = Auth::user();
        
        $medico = Medico::with('usuario')
            ->where('id_usuarioFK', $usuario->idUsuarioPK)
            ->first();
        
        if (!$medico) {
            return redirect()->route('medico.login')->with('error', 'Médico não encontrado.');
        }

        return view('medico.perfilMedico', compact('medico'));
    }

    public function atualizarPerfil(Request $request)
    {
        $usuario = Auth::user();
        
        $medico = Medico::with('usuario')
            ->where('id_usuarioFK', $usuario->idUsuarioPK)
            ->first();

        if (!$medico || !$medico->usuario) {
            return redirect()->route('medico.login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $request->validate([
            'nomeMedico' => 'required|string|max:255',
            'emailUsuario' => [ 
                'required',
                'email',
                'max:255',
                Rule::unique('tbUsuario', 'emailUsuario')->ignore($usuario->idUsuarioPK, 'idUsuarioPK'),
            ],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $medico->nomeMedico = $request->nomeMedico;
        
        $medico->usuario->emailUsuario = $request->emailUsuario;

        if ($request->hasFile('foto')) {

            if ($medico->foto && Storage::disk('public')->exists('fotos/' . $medico->foto)) {
                Storage::disk('public')->delete('fotos/' . $medico->foto);
            }

            $fotoPath = $request->file('foto')->store('fotos', 'public');
            $medico->foto = basename($fotoPath);
        }

        $medico->save();
        $medico->usuario->save();

        return redirect()->route('medico.perfil')->with('success', 'Perfil atualizado com sucesso!');
    }
}