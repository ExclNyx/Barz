<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query()->orderBy('name');
        if (!$request->user() || !in_array($request->user()->role, ['admin','super_admin'], true)) $query->where('is_active', true);
        return ServiceResource::collection($query->get());
    }
    public function indexAdmin()
    {
        return ServiceResource::collection(Service::query()->orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required','string','max:100'], 'description' => ['nullable','string'], 'price' => ['required','numeric','min:0'], 'duration' => ['required','integer','min:5','max:480'], 'image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'], 'image_url' => ['nullable','url','max:2048'], 'is_active' => ['sometimes','boolean']]);
        if ($request->hasFile('image')) $data['image'] = $request->file('image')->store('services', 'public');
        elseif (!empty($data['image_url'])) $data['image'] = $data['image_url'];
        unset($data['image_url']);
        return (new ServiceResource(Service::create($data)))->response()->setStatusCode(201);
    }
    public function show(Service $service) { return new ServiceResource($service); }
    public function update(Request $request, Service $service)
    {
        $data = $request->validate(['name' => ['sometimes','required','string','max:100'], 'description' => ['nullable','string'], 'price' => ['sometimes','required','numeric','min:0'], 'duration' => ['sometimes','required','integer','min:5','max:480'], 'image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'], 'image_url' => ['nullable','url','max:2048'], 'is_active' => ['sometimes','boolean']]);
        if ($request->hasFile('image')) {
            if ($service->image && !str_starts_with($service->image, 'http')) Storage::disk('public')->delete($service->image);
            $data['image'] = $request->file('image')->store('services', 'public');
        } elseif (!empty($data['image_url'])) $data['image'] = $data['image_url'];
        unset($data['image_url']);
        $service->update($data); return new ServiceResource($service->refresh());
    }
    public function destroy(Service $service)
    {
        if ($service->bookings()->exists()) { $service->update(['is_active' => false]); return response()->json(['message' => 'Layanan sudah dipakai dalam booking, jadi dinonaktifkan.']); }
        $service->delete(); return response()->json(['message' => 'Layanan berhasil dihapus.']);
    }
}
