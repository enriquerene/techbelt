<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'quantity',
        'purchase_date',
        'next_maintenance_date',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'next_maintenance_date' => 'date',
        'quantity' => 'integer',
    ];

    // Category constants
    const CATEGORY_FIRST_AID = 'first_aid';
    const CATEGORY_MAINTENANCE = 'maintenance';
    const CATEGORY_MARKETING = 'marketing';
    const CATEGORY_EQUIPMENT = 'equipment';
    const CATEGORY_SUPPLIES = 'supplies';
    const CATEGORY_OTHER = 'other';

    public static function categories(): array
    {
        return [
            self::CATEGORY_FIRST_AID => 'Kit de Primeiros Socorros',
            self::CATEGORY_MAINTENANCE => 'Manutenção',
            self::CATEGORY_MARKETING => 'Marketing',
            self::CATEGORY_EQUIPMENT => 'Equipamento',
            self::CATEGORY_SUPPLIES => 'Suprimentos',
            self::CATEGORY_OTHER => 'Outro',
        ];
    }

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_USE = 'in_use';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_DEPLETED = 'depleted';

    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Disponível',
            self::STATUS_IN_USE => 'Em Uso',
            self::STATUS_MAINTENANCE => 'Manutenção',
            self::STATUS_DEPLETED => 'Esgotado',
        ];
    }

}
