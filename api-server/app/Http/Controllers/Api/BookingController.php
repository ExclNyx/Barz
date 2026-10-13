<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['service','barber','customer'])->orderByDesc('booking_date')->orderByDesc('start_time');
        if (!in_array($request->user()->role, ['admin','super_admin'], true)) $query->where('customer_id', $request->user()->id);
        return BookingResource::collection($query->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['service_id' => ['required','integer','exists:services,id'], 'booking_date' => ['required','date_format:Y-m-d','after_or_equal:today'], 'start_time' => ['required','date_format:H:i'], 'notes' => ['nullable','string','max:2000']]);
        $service = Service::where('id', $data['service_id'])->where('is_active', true)->firstOrFail();
        $start = $data['start_time'];
        $end = date('H:i', strtotime($start) + ((int) $service->duration * 60));
        $closing = DB::table('settings')->where('setting_key', 'closing_time')->value('setting_value') ?: '21:00';
        if ($end > substr((string) $closing, 0, 5)) throw ValidationException::withMessages(['start_time' => ['Jam berakhir melewati jam operasional.']]);
        $barbers = DB::table('barbers')->where('is_active', 1)->orderBy('id')->pluck('id');
        $chosenBarber = null;
        foreach ($barbers as $barberId) {
            $hasSlot = DB::table('time_slots')->whereDate('date', $data['booking_date'])->where('barber_id', $barberId)->where('is_available', 1)->where('start_time', '<=', $start . ':00')->where('end_time', '>=', $start . ':00')->exists();
            $conflict = DB::table('bookings')->whereDate('booking_date', $data['booking_date'])->where('barber_id', $barberId)->whereNotIn('status', ['cancelled','no_show'])->where('start_time', '<', $end . ':00')->where('end_time', '>', $start . ':00')->exists();
            if ($hasSlot && !$conflict) { $chosenBarber = $barberId; break; }
        }
        if (!$chosenBarber) throw ValidationException::withMessages(['start_time' => ['Slot sudah tidak tersedia. Silakan pilih jam lain.']]);
        $booking = Booking::create(['customer_id' => $request->user()->id, 'service_id' => $service->id, 'barber_id' => $chosenBarber, 'booking_date' => $data['booking_date'], 'start_time' => $start . ':00', 'end_time' => $end . ':00', 'status' => 'pending', 'total_price' => $service->price, 'notes' => $data['notes'] ?? null]);
        return (new BookingResource($booking->load(['service','barber','customer'])))->response()->setStatusCode(201);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate(['status' => ['required','in:pending,confirmed,completed,cancelled,no_show']]);
        $booking->update(['status' => $data['status']]);
        return new BookingResource($booking->refresh()->load(['service','barber','customer']));
    }
}
