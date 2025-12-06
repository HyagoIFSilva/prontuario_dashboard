<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medico extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbMedico';
    protected $primaryKey = 'idMedicoPK';

    public $timestamps = false; 

    protected $fillable = [
        'foto',
        'nomeMedico',
        'crmMedico',
        'especialidadeMedico',
        'id_usuarioFK',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuarioFK', 'idUsuarioPK');
    }

    public function unidades()
    {
        return $this->belongsToMany(Unidade::class, 'tbMedicoUnidade', 'idMedicoFK', 'idUnidadeFK');
    }
}

