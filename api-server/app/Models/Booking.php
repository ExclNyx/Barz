<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Booking extends Model
{
    protected $table = 'bookings';
    protected $fillable = ['customer_id', 'service_id', 'barber_id', 'booking_date', 'start_time', 'end_time', 'status', 'total_price', 'notes'];
    protected function casts(): array { return ['booking_date' => 'date:Y-m-d', 'total_price' => 'decimal:2']; }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function service() { return $this->belongsTo(Service::class, 'service_id'); }
    public function barber() { return $this->belongsTo(Barber::class, 'barber_id'); }
}
