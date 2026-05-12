<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['student_id', 'month', 'year', 'amount', 'due_date', 'status', 'paid_at', 'notes'];

    protected $casts = [
        'due_date' => 'date',
        'paid_at'  => 'datetime',
    ];

    const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    const STATUS_LABELS = [
        'unpaid'  => 'Belum Bayar',
        'paid'    => 'Lunas',
        'overdue' => 'Jatuh Tempo',
        'pending' => 'Menunggu Verifikasi',
    ];

    const STATUS_COLORS = [
        'unpaid'  => 'warning',
        'paid'    => 'success',
        'overdue' => 'danger',
        'pending' => 'info',
    ];

    public function getMonthNameAttribute(): string
    {
        return self::MONTHS[$this->month] ?? '-';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
