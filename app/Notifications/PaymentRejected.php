<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentRejected extends Notification
{
    use Queueable;

    protected $invoice;
    protected $payment;
    protected $reason;

    public function __construct($invoice, $payment, $reason)
    {
        $this->invoice = $invoice;
        $this->payment = $payment;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => 'Pembayaran Ditolak',
            'message' => 'Pembayaran SPP ' . $this->invoice->month_name . ' ' . $this->invoice->year . ' ditolak. Alasan: ' . $this->reason,
            'icon'    => 'bi-exclamation-triangle-fill',
            'color'   => 'danger',
            'link'    => route('siswa.payments.index'),
        ];
    }
}
