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
        $query = Booking::with(['service', 'barber', 'customer'])
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time');

        if (!in_array($request->user()->role, ['admin', 'super_admin'], true)) {
            $query->where('customer_id', $request->user()->id);
        }

        return BookingResource::collection($query->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'booking_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $service = Service::query()
            ->whereKey($data['service_id'])
            ->where('is_active', true)
            ->first();

        if (!$service) {
            throw ValidationException::withMessages([
                'service_id' => ['Layanan tidak ditemukan atau sedang nonaktif.'],
            ]);
        }

        $start = $data['start_time'];
        if ($data['booking_date'] === now()->toDateString() && $start <= now()->format('H:i')) {
            throw ValidationException::withMessages([
                'start_time' => ['Jam booking harus lebih dari waktu sekarang.'],
            ]);
        }

        $end = date('H:i', strtotime($start) + ((int) $service->duration * 60));
        $closing = DB::table('settings')
            ->where('setting_key', 'closing_time')
            ->value('setting_value') ?: '21:00';

        if ($end > substr((string) $closing, 0, 5)) {
            throw ValidationException::withMessages([
                'start_time' => ['Jam berakhir melewati jam operasional.'],
            ]);
        }

        // Lock barber rows during the availability check so simultaneous requests
        // cannot both reserve the same barber/time window.
        $booking = DB::transaction(function () use ($request, $data, $service, $start, $end) {
            $barbers = DB::table('barbers')
                ->where('is_active', 1)
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id');

            foreach ($barbers as $barberId) {
                $slot = DB::table('time_slots')
                    ->whereDate('date', $data['booking_date'])
                    ->where('barber_id', $barberId)
                    ->where('is_available', 1)
                    ->where('start_time', '<=', $start . ':00')
                    ->where('end_time', '>', $start . ':00')
                    ->lockForUpdate()
                    ->first(['id']);

                if (!$slot) {
                    continue;
                }

                $conflict = DB::table('bookings')
                    ->whereDate('booking_date', $data['booking_date'])
                    ->where('barber_id', $barberId)
                    ->whereNotIn('status', ['cancelled', 'no_show'])
                    ->where('start_time', '<', $end . ':00')
                    ->where('end_time', '>', $start . ':00')
                    ->exists();

                if ($conflict) {
                    continue;
                }

                return Booking::create([
                    'customer_id' => $request->user()->id,
                    'service_id' => $service->id,
                    'barber_id' => $barberId,
                    'booking_date' => $data['booking_date'],
                    'start_time' => $start . ':00',
                    'end_time' => $end . ':00',
                    'status' => 'pending',
                    'total_price' => $service->price,
                    'notes' => $data['notes'] ?? null,
                ]);
            }

            return null;
        });

        if (!$booking) {
            throw ValidationException::withMessages([
                'start_time' => ['Slot sudah tidak tersedia. Silakan pilih jam lain.'],
            ]);
        }

        return (new BookingResource($booking->load(['service', 'barber', 'customer'])))
            ->response()
            ->setStatusCode(201);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ]);

        $booking->update(['status' => $data['status']]);

        return new BookingResource($booking->refresh()->load(['service', 'barber', 'customer']));
    }
}
