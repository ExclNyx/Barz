<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'description' => $this->description, 'price' => (float) $this->price, 'duration' => (int) $this->duration, 'image' => $this->image ? (str_starts_with((string) $this->image, 'http') ? $this->image : url('/storage/'.$this->image)) : null, 'is_active' => (bool) $this->is_active];
    }
}
