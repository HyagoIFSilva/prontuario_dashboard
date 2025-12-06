<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Enfermeiro;
use App\Models\Recepcionista; // <-- IMPORTANTE

class Unidade extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbUnidade';
    protected $primaryKey = 'idUnidadePK';

    protected $fillable = [
        'nomeUnidade',
        'tipoUnidade',
        'senhaUnidade',
        'telefoneUnidade',
        'cnpjUnidade',
        'emailUnidade',
        'statusAtivoUnidade',
        'logradouroUnidade',
        'numLogradouroUnidade',
        'bairroUnidade',
        'cepUnidade',
        'cidadeUnidade',
        'ufUnidade',
        'estadoUnidade',
        'paisUnidade',
    ];

    protected $hidden = [
        'senhaUnidade',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->senhaUnidade;
    }

    public function getAuthIdentifierName()
    {
        return 'cnpjUnidade';
    }

    public function medicos()
    {
        return $this->belongsToMany(Medico::class, 'tbMedicoUnidade', 'idUnidadeFK', 'idMedicoFK');
    }

    public function enfermeiros()
    {
        return $this->belongsToMany(Enfermeiro::class, 'tbEnfermeiroUnidade', 'idUnidadeFK', 'idEnfermeiroFK');
    }

    /**
     * RELAÇÃO ADICIONADA: Uma Unidade tem muitos Recepcionistas
     */
    public function recepcionistas()
    {
        return $this->hasMany(Recepcionista::class, 'idUnidadeFK', 'idUnidadePK');
    }
}