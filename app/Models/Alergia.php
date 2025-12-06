<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Alergia extends Model
{
    protected $table = 'tbAlergia';
    protected $primaryKey = 'idAlergiaPK';
    protected $fillable = [
        'idPacienteFK',
        'nomeAlergia',
        'tipoAlergia',
        'severidadeAlergia',
        'descAlergia',
    ];
    
    public function paciente()
    {
    return $this->belongsTo(Paciente::class, 'idPacienteFK', 'idPaciente');
    }
}

