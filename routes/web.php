<?php

use Illuminate\Support\Facades\Route;

// ===================================================================================
// --- IMPORTAÇÃO DE CONTROLLERS ---
// ===================================================================================

// --------------------- ADMIN ---------------------
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ConfiguracaoController as AdminConfiguracaoController;
use App\Http\Controllers\Admin\SegurancaController as AdminSegurancaController;
use App\Http\Controllers\Admin\UnidadeController as AdminUnidadeController;

// --------------------- PACIENTE ---------------------
use App\Http\Controllers\Paciente\PacienteController as AdminPacienteController;

// --------------------- MÉDICO ---------------------
use App\Http\Controllers\Medico\LoginController as MedicoLoginController;
use App\Http\Controllers\Medico\MedicoDashboardController;
use App\Http\Controllers\Medico\MedicoConfiguracaoController;
use App\Http\Controllers\Medico\MedicoSegurancaController;
use App\Http\Controllers\Medico\MedicoProntuarioController;
use App\Http\Controllers\Medico\MedicoPdfController;

// --------------------- ENFERMEIRO ---------------------
use App\Http\Controllers\Enfermeiro\LoginController as EnfermeiroLoginController;
use App\Http\Controllers\Enfermeiro\DashboardController as EnfermeiroDashboardController;
use App\Http\Controllers\Enfermeiro\ConfiguracaoController as ConfiguracaoEnfermeiroController;
use App\Http\Controllers\Enfermeiro\SegurancaController as SegurancaEnfermeiroController;
use App\Http\Controllers\Enfermeiro\ProntuarioController as EnfermeiroProntuarioController;

// --------------------- RECEPCIONISTA ---------------------
use App\Http\Controllers\Recepcionista\LoginController as RecepcionistaLoginController;
use App\Http\Controllers\Recepcionista\RecepcionistaDashboardController;
use App\Http\Controllers\Recepcionista\RecepcionistaConfiguracaoController;

// --------------------- UNIDADE ---------------------
use App\Http\Controllers\Unidade\LoginController as UnidadeLoginController;
use App\Http\Controllers\Unidade\DashboardController as UnidadeDashboardController;
use App\Http\Controllers\Unidade\MedicoController;
use App\Http\Controllers\Unidade\EnfermeiroController;
use App\Http\Controllers\Unidade\RecepcionistaController;
use App\Http\Controllers\Unidade\SegurancaController as UnidadeSegurancaController;
use App\Http\Controllers\Unidade\SuporteController as UnidadeSuporteController;
use App\Http\Controllers\Unidade\UnidadeController as UnidadeUnidadeController;

// ===================================================================================
// --- ROTAS PÚBLICAS ---
// ===================================================================================

Route::get('/', fn() => view('geral.index'))->name('home');

Route::middleware('web')->group(function () {
    // ----------------- LOGIN ADMIN -----------------
    Route::get('/loginAdm', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/loginAdm', [AdminLoginController::class, 'login']);
    Route::post('/logoutAdm', [AdminLoginController::class, 'logout'])->name('admin.logout');

    // ----------------- LOGIN MÉDICO -----------------
    Route::get('/loginMedico', [MedicoLoginController::class, 'showLoginForm'])->name('medico.login');
    Route::post('/loginMedico', [MedicoLoginController::class, 'login'])->name('medico.login.submit');
    Route::post('/medico/profile/complete', [MedicoLoginController::class, 'completarPerfil'])->name('api.medico.profile.complete');
    Route::post('/medico/login/check', [MedicoLoginController::class, 'login'])->name('api.medico.login.check');
    Route::post('/medico/alterar-senha-primeiro-login', [MedicoLoginController::class, 'alterarSenhaPrimeiroLogin'])->name('api.medico.alterarSenhaPrimeiroLogin');

    // ----------------- LOGIN ENFERMEIRO -----------------
    Route::get('/enfermeiro/login', [EnfermeiroLoginController::class, 'showLoginForm'])->name('enfermeiro.login');
    Route::post('/enfermeiro/login', [EnfermeiroLoginController::class, 'login'])->name('enfermeiro.login.submit');
    Route::post('/enfermeiro/profile/complete', [EnfermeiroLoginController::class, 'completarPerfil'])->name('api.enfermeiro.profile.complete');
    Route::post('/enfermeiro/login-check', [EnfermeiroLoginController::class, 'login'])->name('api.enfermeiro.login.check');
    Route::post('/enfermeiro/alterar-senha-primeiro-login', [EnfermeiroLoginController::class, 'alterarSenhaPrimeiroLogin'])->name('api.enfermeiro.alterarSenhaPrimeiroLogin');

    // ----------------- LOGIN RECEPCIONISTA -----------------
    Route::get('/loginRecepcionista', [RecepcionistaLoginController::class, 'showLoginForm'])->name('recepcionista.login');
    Route::post('/loginRecepcionista', [RecepcionistaLoginController::class, 'login'])->name('recepcionista.login.submit');

    // ----------------- LOGIN UNIDADE -----------------
    Route::get('/loginUnidade', [UnidadeLoginController::class, 'showLoginForm'])->name('unidade.login');
    Route::post('/loginUnidade', [UnidadeLoginController::class, 'login'])->name('unidade.login.submit');
});

// ===================================================================================
// --- ROTAS PROTEGIDAS DO ADMIN ---
// ===================================================================================
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/ajuda', fn() => view('geral.ajuda'))->name('ajuda');
    Route::post('/ajuda/enviar', [UnidadeSuporteController::class, 'enviarMensagem'])->name('ajuda.enviar');
    Route::get('/perfil', [AdminConfiguracaoController::class, 'perfil'])->name('perfil');
    Route::post('/perfil/update', [AdminConfiguracaoController::class, 'atualizarPerfil'])->name('perfil.update');
    Route::get('/seguranca', [AdminSegurancaController::class, 'showAlterarSenhaForm'])->name('seguranca');
    Route::post('/alterar-senha', [AdminSegurancaController::class, 'alterarSenha'])->name('alterarSenha');
    Route::get('/configuracoes', [AdminConfiguracaoController::class, 'configuracoes'])->name('configuracoes');
    Route::post('pacientes/{paciente}/toggle-status', [AdminPacienteController::class, 'toggleStatus'])->name('pacientes.toggleStatus');
    Route::resource('pacientes', AdminPacienteController::class);
    Route::get('/unidades', [AdminUnidadeController::class, 'index'])->name('unidades.index');
    Route::get('/unidades/create', [AdminUnidadeController::class, 'create'])->name('unidades.create');
    Route::post('/unidades', [AdminUnidadeController::class, 'store'])->name('unidades.store');
    Route::get('/unidades/{unidade}/edit', [AdminUnidadeController::class, 'edit'])->name('unidades.edit');
    Route::put('/unidades/{unidade}', [AdminUnidadeController::class, 'update'])->name('unidades.update');
    Route::delete('/unidades/{unidade}', [AdminUnidadeController::class, 'destroy'])->name('unidades.destroy');
    Route::post('/unidades/{id}/toggle-status', [AdminUnidadeController::class, 'toggleStatus'])->name('unidades.toggle-status');
});

// ===================================================================================
// --- ROTAS PROTEGIDAS DA UNIDADE ---
// ===================================================================================
Route::middleware('auth:unidade')->prefix('unidade')->name('unidade.')->group(function () {
    Route::get('/dashboard', [UnidadeDashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [UnidadeLoginController::class, 'logout'])->name('logout');
    Route::get('/ajuda', [UnidadeUnidadeController::class, 'ajuda'])->name('ajuda');
    Route::get('/perfil', [UnidadeUnidadeController::class, 'perfilUnidade'])->name('perfil');
    Route::get('/seguranca', [UnidadeUnidadeController::class, 'seguranca'])->name('seguranca');
    Route::put('/perfil/update', [UnidadeSegurancaController::class, 'atualizarPerfil'])->name('perfil.update');
    Route::post('/alterar-senha', [UnidadeSegurancaController::class, 'alterarSenha'])->name('alterarSenha');
    
    // ----------------- MÉDICOS -----------------
    Route::get('/manutencaoMedicos', [MedicoController::class, 'index'])->name('manutencaoMedicos');
    Route::get('/cadastroMedico', [MedicoController::class, 'create'])->name('medicos.create');
    Route::post('/medicos/register', [MedicoController::class, 'store'])->name('medicos.register');
    Route::get('/medicos/{id}/editar', [MedicoController::class, 'edit'])->name('medicos.edit');
    Route::put('/medicos/{id}', [MedicoController::class, 'update'])->name('medicos.update');
    Route::get('/medicos/{id}/excluir', [MedicoController::class, 'confirmarExclusao'])->name('medicos.confirmarExclusao');
    Route::delete('/medicos/{id}', [MedicoController::class, 'excluir'])->name('medicos.excluir');
    Route::post('/medicos/{id}/toggle-status', [MedicoController::class, 'toggleStatus'])->name('medicos.toggleStatus');
    Route::post('/medicos/{medico}/unidades', [MedicoController::class, 'syncUnidades'])->name('medicos.syncUnidades');
    
    // ----------------- ENFERMEIROS -----------------
    Route::get('/manutencaoEnfermeiro', [EnfermeiroController::class, 'index'])->name('manutencaoEnfermeiro');
    Route::get('/cadastroEnfermeiro', [EnfermeiroController::class, 'create'])->name('enfermeiro.create');
    Route::post('/enfermeiro/register', [EnfermeiroController::class, 'store'])->name('enfermeiro.register');
    Route::get('/enfermeiro/{id}/editar', [EnfermeiroController::class, 'edit'])->name('enfermeiro.edit');
    Route::put('/enfermeiro/{id}', [EnfermeiroController::class, 'update'])->name('enfermeiro.update');
    Route::get('/enfermeiro/{id}/excluir', [EnfermeiroController::class, 'confirmarExclusao'])->name('enfermeiro.confirmarExclusao');
    Route::delete('/enfermeiro/{id}', [EnfermeiroController::class, 'excluir'])->name('enfermeiro.excluir');
    Route::post('/enfermeiro/{id}/toggle-status', [EnfermeiroController::class, 'toggleStatus'])->name('enfermeiro.toggleStatus');
    Route::post('/enfermeiro/{enfermeiro}/unidades', [EnfermeiroController::class, 'syncUnidades'])->name('enfermeiro.syncUnidades');
    
    // ----------------- RECEPCIONISTAS -----------------
    Route::get('/manutencaoRecepcionista', [RecepcionistaController::class, 'index'])->name('manutencaoRecepcionista');
    Route::get('/recepcionistas/create', [RecepcionistaController::class, 'create'])->name('recepcionistas.create');
    Route::post('/recepcionistas', [RecepcionistaController::class, 'store'])->name('recepcionistas.store');
    Route::get('/recepcionistas/{recepcionista}/edit', [RecepcionistaController::class, 'edit'])->name('recepcionistas.edit');
    Route::put('/recepcionistas/{recepcionista}', [RecepcionistaController::class, 'update'])->name('recepcionistas.update');
    Route::delete('/recepcionistas/{recepcionista}', [RecepcionistaController::class, 'destroy'])->name('recepcionistas.destroy');
    
    // Rotas AJAX para Recepcionistas (sem status)
    Route::get('/recepcionistas/{recepcionista}/quick-view', [RecepcionistaController::class, 'quickView'])->name('recepcionistas.quickView');
    Route::get('/recepcionistas/export', [RecepcionistaController::class, 'export'])->name('recepcionistas.export');
});

// ===================================================================================
// --- ROTAS PROTEGIDAS DO MÉDICO ---
// ===================================================================================
Route::middleware('auth:medico')->prefix('medico')->name('medico.')->group(function () {
    
    // Dashboard e Configurações
    Route::get('/dashboard', [MedicoDashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [MedicoLoginController::class, 'logout'])->name('logout');
    
    // Perfil e Segurança
    Route::get('/perfil', [MedicoConfiguracaoController::class, 'perfil'])->name('perfil');
    Route::put('/perfil/update', [MedicoConfiguracaoController::class, 'atualizarPerfil'])->name('perfil.update');
    Route::get('/seguranca', [MedicoSegurancaController::class, 'showAlterarSenhaForm'])->name('seguranca');
    Route::post('/alterar-senha', [MedicoSegurancaController::class, 'alterarSenha'])->name('alterarSenha');
    
    // Ajuda
    Route::get('/ajuda', fn() => view('medico.ajudaMedico'))->name('ajuda');

    // --- CORREÇÃO: Rotas de Prontuário ---
    Route::get('/prontuario', [MedicoProntuarioController::class, 'index'])->name('prontuario');
    Route::get('/cadastrar-prontuario/{id}', [MedicoProntuarioController::class, 'create'])->name('cadastrarProntuario');
    Route::post('/cadastrar-prontuario/{id}', [MedicoProntuarioController::class, 'store'])->name('prontuario.store');
    Route::get('/prontuario/editar/{id}', [MedicoProntuarioController::class, 'edit'])->name('prontuario.edit');
    Route::put('/prontuario/atualizar/{id}', [MedicoProntuarioController::class, 'update'])->name('prontuario.update');
    Route::delete('/prontuario/deletar/{id}', [MedicoProntuarioController::class, 'destroy'])->name('prontuario.destroy');
    Route::get('/visualizar-prontuario/{id}', [MedicoProntuarioController::class, 'show'])->name('visualizarProntuario');
    Route::get('/prontuario/{id}', [MedicoProntuarioController::class, 'show'])->name('paciente.prontuario');


    Route::get('/pdf/exames/{idConsulta}', [MedicoPdfController::class, 'gerarPdfExames'])->name('pdf.exames');
    Route::get('/pdf/receita/{idConsulta}', [MedicoPdfController::class, 'gerarPdfReceita'])->name('pdf.receita');

});



// ===================================================================================
// --- ROTAS PROTEGIDAS DO ENFERMEIRO ---
// ===================================================================================

Route::middleware('auth:enfermeiro')->prefix('enfermeiro')->name('enfermeiro.')->group(function () {
    Route::get('/dashboard', [EnfermeiroDashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [EnfermeiroLoginController::class, 'logout'])->name('logout');
    Route::get('/perfil', [ConfiguracaoEnfermeiroController::class, 'perfil'])->name('perfil');
    Route::put('/perfil/update', [ConfiguracaoEnfermeiroController::class, 'atualizarPerfil'])->name('perfil.update');
    Route::get('/seguranca', [SegurancaEnfermeiroController::class, 'showAlterarSenhaForm'])->name('seguranca');
    Route::post('/alterar-senha', [SegurancaEnfermeiroController::class, 'alterarSenha'])->name('alterarSenha');
    Route::get('/paciente', fn() => view('enfermeiro.pacientes'))->name('pacientes');
    Route::get('/ajuda', fn() => view('enfermeiro.ajuda'))->name('ajuda');
    Route::get('/prontuario', [EnfermeiroProntuarioController::class, 'index'])->name('prontuario');
    Route::get('/visualizar-prontuario/{id}', [EnfermeiroProntuarioController::class, 'show'])->name('visualizarProntuario');
    Route::get('/prontuario/{id}/anotacao/criar', [EnfermeiroProntuarioController::class, 'create'])->name('anotacao.create');
    Route::post('/prontuario/{id}/anotacao/salvar', [EnfermeiroProntuarioController::class, 'store'])->name('anotacao.store');
    Route::get('/prontuario/anotacao/editar/{id}', [EnfermeiroProntuarioController::class, 'edit'])->name('anotacao.edit');
    Route::put('/prontuario/anotacao/atualizar/{id}', [EnfermeiroProntuarioController::class, 'update'])->name('anotacao.update');
    Route::delete('/prontuario/anotacao/deletar/{id}', [EnfermeiroProntuarioController::class, 'destroy'])->name('anotacao.destroy');
});

// ===================================================================================
// --- ROTAS DO RECEPCIONISTA (PROTEGIDAS) ---
// ===================================================================================
Route::middleware('auth:recepcionista')->prefix('recepcionista')->name('recepcionista.')->group(function () {
    
    Route::get('/dashboard', [RecepcionistaDashboardController::class, 'index'])->name('dashboard');
    
    Route::post('/acolhimento/salvar', [RecepcionistaDashboardController::class, 'store'])->name('acolhimento.store');
    
    Route::get('/pacientes/buscar', [AdminPacienteController::class, 'buscar'])->name('pacientes.buscar');
    
    Route::post('/logout', [RecepcionistaLoginController::class, 'logout'])->name('logout');
    Route::get('/perfil', [RecepcionistaConfiguracaoController::class, 'perfil'])->name('perfil');
    Route::post('/perfil/atualizar', [RecepcionistaConfiguracaoController::class, 'atualizarPerfil'])->name('atualizarPerfil');
    Route::post('/trocar-senha', [RecepcionistaConfiguracaoController::class, 'trocarSenha'])->name('trocarSenha');
});