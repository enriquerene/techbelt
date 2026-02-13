<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Payment method constants
    const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';
    const PAYMENT_METHOD_DEBIT_CARD = 'debit_card';
    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_PIX = 'pix';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    public static function paymentMethodOptions(): array
    {
        return [
            self::PAYMENT_METHOD_CREDIT_CARD => 'Cartão de Crédito',
            self::PAYMENT_METHOD_DEBIT_CARD => 'Cartão de Débito',
            self::PAYMENT_METHOD_CASH => 'Dinheiro',
            self::PAYMENT_METHOD_PIX => 'PIX',
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Transferência Bancária',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_COMPLETED => 'Concluído',
            self::STATUS_FAILED => 'Falhou',
            self::STATUS_REFUNDED => 'Reembolsado',
        ];
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'paid_at' => now(),
        ]);
    }
}
