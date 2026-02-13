<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'description',
        'amount',
        'category',
        'date',
        'payment_method',
        'notes',
        'user_id',
        'staff_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(Instructor::class, 'staff_id');
    }

    // Category constants
    const CATEGORY_STAFF_PAYMENT = 'staff_payment';
    const CATEGORY_MAINTENANCE = 'maintenance';
    const CATEGORY_MARKETING = 'marketing';
    const CATEGORY_SUPPLIES = 'supplies';
    const CATEGORY_EQUIPMENT = 'equipment';
    const CATEGORY_OTHER = 'other';

    public static function categories(): array
    {
        return [
            self::CATEGORY_STAFF_PAYMENT => 'Pagamento de Funcionário',
            self::CATEGORY_MAINTENANCE => 'Manutenção',
            self::CATEGORY_MARKETING => 'Marketing',
            self::CATEGORY_SUPPLIES => 'Suprimentos',
            self::CATEGORY_EQUIPMENT => 'Equipamento',
            self::CATEGORY_OTHER => 'Outro',
        ];
    }

    // Payment method constants
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';
    const PAYMENT_METHOD_PIX = 'pix';

    public static function paymentMethods(): array
    {
        return [
            self::PAYMENT_METHOD_CASH => 'Dinheiro',
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Transferência Bancária',
            self::PAYMENT_METHOD_CREDIT_CARD => 'Cartão de Crédito',
            self::PAYMENT_METHOD_PIX => 'PIX',
        ];
    }
}
