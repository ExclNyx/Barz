<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SlotController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['service_id' => ['required','integer','exists:services,id'], 'date' => ['required','date_format:Y-m-d','after_or_equal:today'], 'barber_id' => ['nullable','integer','exists:barbers,id']]);
        $service = DB::table('services')->where('id', $data['service_id'])->where('is_active', 1)->first();
        if (!$service) return response()->json(['message' => 'Layanan tidak aktif.'], 404);
        $duration = (int) $service->duration;
        $closing = DB::table('settings')->where('setting_key', 'closing_time')->value('setting_value') ?: '21:00';
        $slotQuery = DB::table('time_slots')->whereDate('date', $data['date'])->where('is_available', 1)->orderBy('start_time');
        if (!empty($data['barber_id'])) $slotQuery->where('barber_id', $data['barber_id']);
        $slotStarts = $slotQuery->get(['start_time'])->pluck('start_time')->unique()->values();
        $slots = [];
        foreach ($slotStarts as $rawStart) {
            $start = substr((string) $rawStart, 0, 5);
            if ($data['date'] === now()->toDateString() && $start <= now()->format('H:i')) {
                continue;
            }
            $end = date('H:i', strtotime($start) + $duration * 60);
            $eligible = DB::table('barbers')->where('is_active', 1);
            if (!empty($data['barber_id'])) $eligible->where('id', $data['barber_id']);
            $barbers = $eligible->pluck('id');
            $available = false;
            if ($end <= substr((string) $closing, 0, 5)) {
                foreach ($barbers as $barberId) {
                    $hasSlot = DB::table('time_slots')->whereDate('date', $data['date'])->where('barber_id', $barberId)->where('is_available', 1)->where('start_time', '<=', $start . ':00')->where('end_time', '>', $start . ':00')->exists();
                    $conflict = DB::table('bookings')->whereDate('booking_date', $data['date'])->where('barber_id', $barberId)->whereNotIn('status', ['cancelled','no_show'])->where('start_time', '<', $end . ':00')->where('end_time', '>', $start . ':00')->exists();
                    if ($hasSlot && !$conflict) { $available = true; break; }
                }
            }
            $slots[] = ['start' => $start, 'end' => $end, 'available' => $available];
        }
        return response()->json(['date' => $data['date'], 'service_id' => (int) $data['service_id'], 'slots' => $slots]);
    }
}
