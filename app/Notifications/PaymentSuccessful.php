<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentSuccessful extends Notification
{
    use Queueable;

    protected $invoice;
    protected $payment;

    public function __construct($invoice, $payment)
    {
        $this->invoice = $invoice;
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => 'Pembayaran Berhasil',
            'message' => 'Pembayaran SPP ' . $this->invoice->month_name . ' ' . $this->invoice->year . ' sebesar ' . currency($this->invoice->amount) . ' telah berhasil diverifikasi.',
            'icon'    => 'bi-check-circle-fill',
            'color'   => 'success',
            'link'    => route('siswa.payments.index'),
        ];
    }
}
