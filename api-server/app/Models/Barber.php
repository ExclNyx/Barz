<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Barber extends Model
{
    protected $table = 'barbers';
    protected $fillable = ['name', 'photo', 'specialization', 'experience_years', 'is_active'];
    protected function casts(): array { return ['experience_years' => 'integer', 'is_active' => 'boolean']; }
    public function bookings() { return $this->hasMany(Booking::class, 'barber_id'); }
}
