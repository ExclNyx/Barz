<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('users')) Schema::create('users', function (Blueprint $t) { $t->id(); $t->string('name',100); $t->string('email',100)->unique(); $t->string('phone',20)->nullable(); $t->string('password'); $t->string('role',30)->default('customer')->index(); $t->timestamp('email_verified_at')->nullable(); $t->rememberToken(); $t->timestamps(); });
        if (!Schema::hasTable('services')) Schema::create('services', function (Blueprint $t) { $t->id(); $t->string('name',100); $t->text('description')->nullable(); $t->decimal('price',10,2); $t->unsignedInteger('duration'); $t->string('image')->nullable(); $t->boolean('is_active')->default(true)->index(); $t->timestamps(); });
        if (!Schema::hasTable('barbers')) Schema::create('barbers', function (Blueprint $t) { $t->id(); $t->string('name',100); $t->string('photo')->nullable(); $t->string('specialization')->nullable(); $t->unsignedInteger('experience_years')->default(0); $t->boolean('is_active')->default(true)->index(); $t->timestamps(); });
        if (!Schema::hasTable('bookings')) Schema::create('bookings', function (Blueprint $t) { $t->id(); $t->foreignId('customer_id')->constrained('users')->cascadeOnDelete(); $t->foreignId('service_id')->constrained('services')->restrictOnDelete(); $t->foreignId('barber_id')->nullable()->constrained('barbers')->nullOnDelete(); $t->date('booking_date')->index(); $t->time('start_time'); $t->time('end_time'); $t->enum('status',['pending','confirmed','completed','cancelled','no_show'])->default('pending')->index(); $t->decimal('total_price',10,2); $t->text('notes')->nullable(); $t->timestamps(); });
        if (!Schema::hasTable('schedules')) Schema::create('schedules', function (Blueprint $t) { $t->id(); $t->foreignId('barber_id')->nullable()->constrained('barbers')->cascadeOnDelete(); $t->date('date')->index(); $t->time('start_time'); $t->time('end_time'); $t->boolean('is_available')->default(true); $t->enum('type',['working_hours','break','blocked'])->default('working_hours'); $t->timestamps(); $t->index(['barber_id','date']); });
        if (!Schema::hasTable('time_slots')) Schema::create('time_slots', function (Blueprint $t) { $t->id(); $t->date('date')->index(); $t->time('start_time'); $t->time('end_time'); $t->foreignId('barber_id')->nullable()->constrained('barbers')->cascadeOnDelete(); $t->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete(); $t->boolean('is_available')->default(true)->index(); $t->timestamps(); $t->index(['barber_id','date']); });
        if (!Schema::hasTable('chats')) Schema::create('chats', function (Blueprint $t) { $t->id(); $t->foreignId('customer_id')->constrained('users')->cascadeOnDelete(); $t->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete(); $t->enum('status',['active','closed'])->default('active')->index(); $t->timestamps(); });
        if (!Schema::hasTable('chat_messages')) Schema::create('chat_messages', function (Blueprint $t) { $t->id(); $t->foreignId('chat_id')->constrained('chats')->cascadeOnDelete(); $t->foreignId('sender_id')->constrained('users')->cascadeOnDelete(); $t->text('message'); $t->boolean('is_read')->default(false)->index(); $t->timestamps(); });
        if (!Schema::hasTable('settings')) Schema::create('settings', function (Blueprint $t) { $t->id(); $t->string('setting_key',100)->unique(); $t->text('setting_value')->nullable(); $t->timestamps(); });
    }
    public function down(): void {
        // Keep tables because the legacy PHP web app shares this database.
    }
};
