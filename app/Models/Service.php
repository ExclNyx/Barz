<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Service extends Model
{
    protected $table = 'services';
    protected $fillable = ['name', 'description', 'price', 'duration', 'image', 'is_active'];
    protected function casts(): array { return ['price' => 'decimal:2', 'duration' => 'integer', 'is_active' => 'boolean']; }
    public function bookings() { return $this->hasMany(Booking::class, 'service_id'); }
}
