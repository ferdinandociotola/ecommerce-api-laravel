<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Message;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build(): self
    {
        return $this->subject('Conferma Ordine #' . $this->order->id)
                    ->html($this->getHtml());
    }

    private function getHtml(): string
    {
        $items = '';
        foreach ($this->order->orderItems as $item) {
            $subtotal = $item->price_snapshot * $item->quantity;
            $items .= "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>{$item->product->name}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: center;'>{$item->quantity}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: right;'>€" . number_format($item->price_snapshot, 2) . "</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align: right;'>€" . number_format($subtotal, 2) . "</td>
                </tr>";
        }

        $total = number_format($this->order->total, 2);

        return "
        <div style='max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;'>
            <div style='background: #2d3748; color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0;'>Conferma Ordine</h1>
                <p style='margin: 10px 0 0;'>Ordine #{$this->order->id}</p>
            </div>

            <div style='padding: 30px; border: 1px solid #e2e8f0;'>
                <p style='color: #4a5568;'>Ciao,<br>grazie per il tuo ordine!</p>

                <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                    <thead>
                        <tr style='background: #edf2f7;'>
                            <th style='padding: 10px; text-align: left;'>Prodotto</th>
                            <th style='padding: 10px; text-align: center;'>Qty</th>
                            <th style='padding: 10px; text-align: right;'>Prezzo</th>
                            <th style='padding: 10px; text-align: right;'>Subtotale</th>
                        </tr>
                    </thead>
                    <tbody>
                        $items
                    </tbody>
                </table>

                <div style='text-align: right; padding: 15px; background: #edf2f7; border-radius: 5px;'>
                    <strong style='font-size: 18px;'>Totale: €$total</strong>
                </div>

                <div style='margin-top: 20px; padding: 15px; background: #ebf8ff; border-radius: 5px;'>
                    <p style='margin: 0; color: #2b6cb0;'>
                        <strong>Status:</strong> " . ucfirst($this->order->status) . "<br>
                        <strong>Pagamento:</strong> " . ucfirst($this->order->payment_status) . "
                    </p>
                </div>

                <p style='color: #718096; font-size: 14px; margin-top: 30px;'>
                    Se hai problemi con il tuo ordine, contattaci.<br>
                    Grazie per la scelta!
                </p>
            </div>
        </div>";
    }
}