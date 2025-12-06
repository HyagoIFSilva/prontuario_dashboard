<?php

namespace App\Http\Controllers\Unidade;

use App\Http\Controllers\Controller;
use App\Models\Recepcionista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RecepcionistaController extends Controller
{
    public function index()
    {
        $unidade = Auth::guard('unidade')->user();

        if (!$unidade) {
            return redirect()->route('unidade.login')->with('error', 'Você precisa estar logado.');
        }

        $recepcionistas = $unidade->recepcionistas;

        return view('unidade.manutencaoRecepcionista', compact('recepcionistas'));
    }

    public function create()
    {
        return view('unidade.cadastroRecepcionista');
    }

    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'nomeRecepcionista' => 'required|string|max:255',
                'genero' => [
                    'required',
                    'string',
                    Rule::in(['Feminino', 'Masculino', 'Outro']),
                ],
                'emailRecepcionista' => ['required', 'email', 'unique:tbRecepcionista,emailRecepcionista'],
                'senhaRecepcionista' => 'required|string|min:6',
            ]);

            $unidade = Auth::guard('unidade')->user();

            if (!$unidade) {
                return response()->json(['success' => false, 'message' => 'Unidade não autenticada.'], 401);
            }

            $data['senhaRecepcionista'] = Hash::make($data['senhaRecepcionista']);
            $data['statusAtivoRecepcionista'] = 1;
            $data['idUnidadeFK'] = $unidade->idUnidadePK;

            $recepcionista = Recepcionista::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Recepcionista cadastrado com sucesso!',
                'idRecepcionistaPK' => $recepcionista->idRecepcionistaPK
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao cadastrar: ' . $e->getMessage()], 500);
        }
    }

    public function show(Recepcionista $recepcionista)
    {
        return view('unidade.visualizarRecepcionista', compact('recepcionista'));
    }

    public function edit(Recepcionista $recepcionista)
    {
        return view('unidade.editarRecepcionista', compact('recepcionista'));
    }

    public function update(Request $request, Recepcionista $recepcionista)
    {
        $data = $request->validate([
            'nomeRecepcionista' => 'sometimes|string|max:255',
            'genero' => [
                'sometimes',
                'string',
                Rule::in(['Feminino', 'Masculino', 'Outro']),
            ],
            'emailRecepcionista' => [
                'sometimes',
                'email',
                Rule::unique('tbRecepcionista')->ignore($recepcionista->idRecepcionistaPK, 'idRecepcionistaPK')
            ],
            'senhaRecepcionista' => 'sometimes|nullable|string|min:6',
        ]);

        if (!empty($data['senhaRecepcionista'])) {
            $data['senhaRecepcionista'] = Hash::make($data['senhaRecepcionista']);
        } else {
            unset($data['senhaRecepcionista']);
        }

        $recepcionista->update($data);

        return redirect()->route('unidade.manutencaoRecepcionista')
            ->with('success', 'Recepcionista atualizado com sucesso!');
    }

    public function destroy(Recepcionista $recepcionista)
    {
        $recepcionista->delete();
        return redirect()->route('unidade.manutencaoRecepcionista')
            ->with('success', 'Recepcionista excluído com sucesso!');
    }

    public function quickView(Recepcionista $recepcionista)
    {
        return response()->json([
            'id' => $recepcionista->idRecepcionistaPK,
            'nome' => $recepcionista->nomeRecepcionista,
            'email' => $recepcionista->emailRecepcionista,
            'genero' => $recepcionista->genero,
            'status' => $recepcionista->statusAtivoRecepcionista,
            'created_at' => $recepcionista->created_at->format('d/m/Y'),
        ]);
    }

    public function export()
    {
        $unidade = Auth::guard('unidade')->user();

        if (!$unidade) {
            return redirect()->route('unidade.login')->with('error', 'Você precisa estar logado.');
        }

        $recepcionistas = $unidade->recepcionistas;

        $csv = "Nome,Gênero,Email,Status,Cadastrado em\n";

        foreach ($recepcionistas as $r) {
            $status = $r->statusAtivoRecepcionista == 1 ? 'Ativo' : 'Inativo';
            $data = $r->created_at->format('d/m/Y');

            $csv .= "{$r->nomeRecepcionista},{$r->genero},{$r->emailRecepcionista},{$status},{$data}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=\"recepcionistas.csv\"',
        ]);
    }

    public function toggleStatus($id)
    {
        $recepcionista = Recepcionista::findOrFail($id);

        $novo = $recepcionista->statusAtivoRecepcionista == 1 ? 0 : 1;

        $recepcionista->update([
            'statusAtivoRecepcionista' => $novo
        ]);

        $msg = $novo ? 'ativado' : 'desativado';

        return redirect()->route('unidade.manutencaoRecepcionista')
            ->with('success', "O recepcionista foi {$msg} com sucesso!");
    }
}
