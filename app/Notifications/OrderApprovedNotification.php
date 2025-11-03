<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderApprovedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $approver;
    protected $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $approver, $notes = null)
    {
        $this->order = $order;
        $this->approver = $approver;
        $this->notes = $notes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->nomor_order,
            'approver_name' => $this->approver->name,
            'approved_at' => now()->format('d/m/Y H:i'),
            'notes' => $this->notes,
            'message' => "Order {$this->order->nomor_order} telah disetujui oleh {$this->approver->name}",
            'url' => route('orders.show', $this->order->id),
        ];
    }
}
