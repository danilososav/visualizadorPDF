<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';
    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'numero',
        'fecha_emision',
        'fecha_vencimiento',
        'tipo_operacion',
        'desc_tipo_operacion',
        'tipo_impuesto',
        'moneda',
        'tasa_cambio',
        'subtotal',
        'descuento',
        'base_gravable',
        'iva',
        'total',
        'estado',
        'xml_contenido',
        'xml_archivo',
        'firma_timestamp',
        'numero_timbre',
        'codigo_seguridad',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'base_gravable' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'tasa_cambio' => 'decimal:2',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function items()
    {
        return $this->hasMany(ItemFactura::class, 'factura_id');
    }
}
