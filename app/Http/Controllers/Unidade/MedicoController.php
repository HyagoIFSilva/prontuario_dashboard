<?php

namespace App\Http\Controllers\Unidade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medico;
use App\Models\Usuario;
use Illuminate\Validation\ValidationException;
use App\Mail\emailMedico;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class MedicoController extends Controller
{
    public function index(Request $request)
    {

        $unidade = auth()->guard('unidade')->user();


        $query = $unidade->medicos()->with('usuario');


        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where('nomeMedico', 'LIKE', "%{$searchTerm}%")
                ->orWhere('crmMedico', 'LIKE', "%{$searchTerm}%")
                ->orWhereHas('usuario', function ($q) use ($searchTerm) {
                    $q->where('emailUsuario', 'LIKE', "%{$searchTerm}%");
                });
        }


        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'ativo') {
                $query->whereHas('usuario', function ($q) {
                    $q->where('statusAtivoUsuario', 1);
                });
            } elseif ($status === 'inativo') {
                $query->whereHas('usuario', function ($q) {
                    $q->where('statusAtivoUsuario', 0);
                });
            }
        }


        $medicos = $query->orderBy('nomeMedico', 'ASC')->get();


        $totalMedicos = $medicos->count();
        $ativosCount   = $medicos->where('usuario.statusAtivoUsuario', 1)->count();
        $inativosCount = $medicos->where('usuario.statusAtivoUsuario', 0)->count();
        $novosCount    = $medicos->where('created_at', '>=', now()->startOfMonth())->count();

        return view('unidade.manutencaoMedicos', compact(
            'medicos',
            'totalMedicos',
            'ativosCount',
            'inativosCount',
            'novosCount'
        ));
    }

    public function create()
    {
        $unidadeLogada = auth()->guard('unidade')->user();
        return view('unidade.cadastroMedico', compact('unidadeLogada'));
    }

    public function store(Request $request)
    {
        try {

            $unidadeLogada = auth()->guard('unidade')->user();

            $validated = $request->validate([
                'nomeMedico'          => 'required|string|max:255',
                'crmMedico'           => 'required|string|max:20|unique:tbMedico,crmMedico',
                'emailUsuario'        => 'required|email|max:255|unique:tbUsuario,emailUsuario',
                'especialidadeMedico' => 'nullable|string|max:100',
                'genero'              => 'required|string|in:Masculino,Feminino,Outro',
                'unidade_id'          => 'required|exists:tbUnidade,idUnidadePK',
            ], [
                'nomeMedico.required'  => 'O nome do médico é obrigatório.',
                'crmMedico.required'   => 'O CRM é obrigatório.',
                'emailUsuario.required' => 'O e-mail é obrigatório.',
                'emailUsuario.unique'  => 'Este e-mail já está cadastrado.',
                'genero.required'      => 'O gênero é obrigatório.',
                'unidade_id.required'  => 'Unidade é obrigatória.',
            ]);

            $senhaTemporaria = Str::random(10);

            $usuario = new Usuario();
            $usuario->nomeUsuario        = $request->nomeMedico;
            $usuario->emailUsuario       = $request->emailUsuario;
            $usuario->senhaUsuario       = Hash::make($senhaTemporaria);
            $usuario->statusAtivoUsuario = 1;
            $usuario->statusSenhaUsuario = true;
            $usuario->save();

            $medico = new Medico();
            $medico->id_usuarioFK       = $usuario->idUsuarioPK;
            $medico->nomeMedico         = $request->nomeMedico;
            $medico->crmMedico          = $request->crmMedico;
            $medico->especialidadeMedico = $request->input('especialidadeMedico', '');
            $medico->genero             = $request->genero;
            $medico->save();

            $medico->unidades()->attach($request->unidade_id);

            Mail::to($usuario->emailUsuario)->send(new emailMedico($usuario, $senhaTemporaria));

            return response()->json([
                'success' => true,
                'message' => 'Médico cadastrado com sucesso!'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao cadastrar médico: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $medico = Medico::with('usuario')->findOrFail($id);
        return view('unidade.editarMedico', compact('medico'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nomeMedico'   => 'required|string|max:255',
            'nomeUsuario'  => 'required|string|max:255',
            'emailUsuario' => 'required|email|max:255',
            'genero'       => 'required|string|max:20',
        ]);

        $medico = Medico::with('usuario')->findOrFail($id);

        $medico->update([
            'nomeMedico' => $request->nomeMedico,
            'genero'     => $request->genero,
        ]);

        $usuario = $medico->usuario;
        $usuario->nomeUsuario  = $request->nomeUsuario;
        $usuario->emailUsuario = $request->emailUsuario;

        if ($request->filled('senhaUsuario')) {
            $usuario->senhaUsuario = bcrypt($request->senhaUsuario);
        }

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('public/fotos');
            $usuario->foto = basename($fotoPath);
        }

        $usuario->save();

        return redirect()
            ->route('unidade.manutencaoMedicos')
            ->with('success', 'Dados atualizados com sucesso!');
    }

    public function toggleStatus($id)
    {
        $medico = Medico::with('usuario')->findOrFail($id);

        if (!$medico->usuario) {
            return redirect()
                ->route('unidade.manutencaoMedicos')
                ->with('error', 'Este médico não está vinculado a um usuário.');
        }

        $usuario = $medico->usuario;
        $novoStatus = !$usuario->statusAtivoUsuario;

        $usuario->statusAtivoUsuario = $novoStatus;
        $usuario->save();

        $acao = $novoStatus ? 'ativado' : 'desativado';

        return redirect()
            ->route('unidade.manutencaoMedicos')
            ->with('success', "O médico foi {$acao} com sucesso!");
    }

    public function quickView(Medico $medico)
    {
        try {
            $unidade = auth()->guard('unidade')->user();

            if (!$medico->unidades->contains($unidade->idUnidadePK)) {
                return response()->json([
                    'error' => 'Acesso não autorizado',
                    'message' => 'Este médico não está associado à sua unidade'
                ], 403);
            }

            $data = [
                'id' => $medico->idMedicoPK,
                'nome' => $medico->nomeMedico ?? 'Não informado',
                'crm' => $medico->crmMedico ?? 'Não informado',
                'especialidade' => $medico->especialidadeMedico ?? 'Não informado',
                'genero' => $medico->genero ?? 'Não informado',
                'email' => $medico->usuario->emailUsuario ?? 'Não informado',
                'status' => $medico->usuario->statusAtivoUsuario ?? 0,
                'created_at' => $medico->created_at ? $medico->created_at->format('d/m/Y H:i') : 'Não informado',
                'updated_at' => $medico->updated_at ? $medico->updated_at->format('d/m/Y H:i') : 'Não informado',
                'foto' => $medico->usuario->foto ?? null,
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar informações do médico',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
