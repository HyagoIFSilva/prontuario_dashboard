<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unidade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Mail\UnidadeAccessCredentials;
use Illuminate\Validation\Rule;

class UnidadeController extends Controller
{
    /**
     * Exibe a lista de unidades.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtém todas as unidades para exibição
        $unidades = Unidade::all();
        return view('admin.manutencaoUnidades', compact('unidades'));
    }

    /**
     * Mostra o formulário de criação de nova unidade.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.cadastroUnidade');
    }

    /**
     * Armazena uma nova unidade no banco de dados.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nomeUnidade' => 'required|string|max:255',
                'cnpjUnidade' => ['required', 'string', 'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/', Rule::unique('tbUnidade', 'cnpjUnidade')],
                'emailUnidade' => ['required', 'email', 'max:255', Rule::unique('tbUnidade', 'emailUnidade')],
                'telefoneUnidade' => 'nullable|string|max:20',
                'tipoUnidade' => 'nullable|string|max:100',

                'cepUnidade' => 'required|string|max:9',
                'logradouroUnidade' => 'required|string|max:255',
                'numLogradouroUnidade' => 'required|string|max:20',
                'bairroUnidade' => 'required|string|max:255',
                'cidadeUnidade' => 'required|string|max:255',
                'ufUnidade' => 'required|string|max:2',
                'estadoUnidade' => 'required|string|max:255',
                'paisUnidade' => 'required|string|max:255',
            ], [
                'cnpjUnidade.unique' => 'Este CNPJ já está cadastrado.',
                'cnpjUnidade.regex' => 'O CNPJ deve estar no formato 00.000.000/0000-00.',
                'emailUnidade.unique' => 'Este e-mail já está cadastrado.',
                'numLogradouroUnidade.required' => 'O campo Número é obrigatório.',
                'estadoUnidade.required' => 'O campo Estado é obrigatório.',
                'paisUnidade.required' => 'O campo País é obrigatório.',
            ]);

            DB::beginTransaction();

            // Lógica de criação da unidade usando atribuição manual
            $senhaTemporaria = Str::random(10);

            // Cria a unidade e atribui os valores
            $unidade = new Unidade();
            $unidade->nomeUnidade = $request->nomeUnidade;
            $unidade->cnpjUnidade = $request->cnpjUnidade;
            $unidade->emailUnidade = $request->emailUnidade;
            $unidade->telefoneUnidade = $request->telefoneUnidade;
            $unidade->tipoUnidade = $request->tipoUnidade;

            // Campos de endereço
            $unidade->cepUnidade = $request->cepUnidade;
            $unidade->logradouroUnidade = $request->logradouroUnidade;
            $unidade->numLogradouroUnidade = $request->numLogradouroUnidade;
            $unidade->bairroUnidade = $request->bairroUnidade;
            $unidade->cidadeUnidade = $request->cidadeUnidade;
            $unidade->ufUnidade = $request->ufUnidade;
            $unidade->estadoUnidade = $request->estadoUnidade;
            $unidade->paisUnidade = $request->paisUnidade;


            $unidade->statusAtivoUnidade = $request->input('statusAtivoUnidade', true);
            $unidade->senhaUnidade = Hash::make($senhaTemporaria);



            $unidade->save();

            $credentials = [
                'login' => $unidade->cnpjUnidade,
                'password' => $senhaTemporaria,
                'unidadeNome' => $unidade->nomeUnidade
            ];

            Mail::to($unidade->emailUnidade)->send(new \App\Mail\EmailUnidade($unidade, $senhaTemporaria));
            

            DB::commit();

            return redirect()->route('admin.unidades.index')
                ->with('success', 'Unidade cadastrada com sucesso! A senha foi enviada por e-mail para ' . $unidade->emailUnidade . '.');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cadastrar unidade: ' . $e->getMessage());
            return back()->with('error', 'Erro ao cadastrar unidade ou enviar e-mail. Detalhes: ' . $e->getMessage());
        }
    }

    /**
     * Mostra o formulário de edição de unidade.
     *
     * @param \App\Models\Unidade
     * @return \Illuminate\View\View
     */
    public function edit(Unidade $unidade)
    {
        return view('admin.editarUnidade', compact('unidade'));
    }

    /**
     * Atualiza a unidade no banco de dados.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Unidade $unidade
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Unidade $unidade)
    {
        $validatedData = $request->validate([
            'nomeUnidade' => 'required|string|max:255',
            'cnpjUnidade' => ['required', 'string', 'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/', Rule::unique('tbUnidade', 'cnpjUnidade')->ignore($unidade->idUnidadePK, 'idUnidadePK')],
            'emailUnidade' => ['required', 'email', 'max:255', Rule::unique('tbUnidade', 'emailUnidade')->ignore($unidade->idUnidadePK, 'idUnidadePK')],
            'telefoneUnidade' => 'nullable|string|max:20',
            'tipoUnidade' => 'nullable|string|max:100',

            'cepUnidade' => 'required|string|max:9',
            'logradouroUnidade' => 'required|string|max:255',
            'numLogradouroUnidade' => 'required|string|max:20',
            'bairroUnidade' => 'required|string|max:255',
            'cidadeUnidade' => 'required|string|max:255',
            'ufUnidade' => 'required|string|max:2',
            'estadoUnidade' => 'required|string|max:255',
            'paisUnidade' => 'required|string|max:255',
        ], [
            'nomeUnidade.required' => 'O campo Nome da Unidade é obrigatório.',
            'cnpjUnidade.required' => 'O campo CNPJ é obrigatório.',
            'emailUnidade.required' => 'O campo E-mail é obrigatório.',
            'cepUnidade.required' => 'O campo CEP é obrigatório.',
            'logradouroUnidade.required' => 'O campo Logradouro é obrigatório.',
            'bairroUnidade.required' => 'O campo Bairro é obrigatório.',
            'cidadeUnidade.required' => 'O campo Cidade é obrigatório.',
            'ufUnidade.required' => 'O campo UF é obrigatório.',
            'cnpjUnidade.unique' => 'Este CNPJ já está cadastrado em outra unidade.',
            'emailUnidade.unique' => 'Este e-mail já está cadastrado em outra unidade.',
            'numLogradouroUnidade.required' => 'O campo Número é obrigatório.',
            'estadoUnidade.required' => 'O campo Estado é obrigatório.',
            'paisUnidade.required' => 'O campo País é obrigatório.',
        ]);

        try {

            $unidadeData = $validatedData;

            $unidade->update($unidadeData);

            return redirect()->route('admin.unidades.index')
                             ->with('success', 'Unidade "' . $unidade->nomeUnidade . '" atualizada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar unidade: ' . $e->getMessage());
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Ocorreu um erro ao atualizar a unidade. Tente novamente.');
        }
    }

    /**
     * Remove a unidade do banco de dados.
     *
     * @param \App\Models\Unidade $unidade
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Unidade $unidade)
    {
        try {
            $unidadeNome = $unidade->nomeUnidade;
            $unidade->delete();

            return redirect()->route('admin.unidades.index')
                             ->with('success', 'Unidade "' . $unidadeNome . '" excluída com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir unidade: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Ocorreu um erro ao excluir a unidade. Tente novamente.');
        }
    }

    /**
     * Alterna o status (ativo/inativo) da unidade.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus($id)
    {
        try {
            $unidade = Unidade::findOrFail($id);
            $unidade->statusAtivoUnidade = !$unidade->statusAtivoUnidade;
            $unidade->save();

            $status = $unidade->statusAtivoUnidade ? 'ativa' : 'inativa';
            return redirect()->back()->with('success', 'Status da unidade "' . $unidade->nomeUnidade . '" alterado para ' . $status . ' com sucesso.');

        } catch (\Exception $e) {
            Log::error('Erro ao alterar status da unidade: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível alterar o status da unidade.');
        }
    }
}
