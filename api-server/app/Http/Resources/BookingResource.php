<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'booking_date' => (string) $this->booking_date, 'start_time' => substr((string) $this->start_time, 0, 5), 'end_time' => substr((string) $this->end_time, 0, 5), 'status' => $this->status, 'total_price' => (float) $this->total_price, 'notes' => $this->notes, 'service' => $this->whenLoaded('service', fn () => ['id' => $this->service->id, 'name' => $this->service->name, 'duration' => $this->service->duration]), 'barber' => $this->whenLoaded('barber', fn () => $this->barber ? ['id' => $this->barber->id, 'name' => $this->barber->name] : null), 'customer' => $this->whenLoaded('customer', fn () => ['id' => $this->customer->id, 'name' => $this->customer->name, 'email' => $this->customer->email])];
    }
}
