<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('users')) DB::statement("ALTER TABLE users MODIFY role VARCHAR(30) NOT NULL DEFAULT 'customer'");
    }
    public function down(): void {
        // Intentionally does not revert role to ENUM because super_admin accounts may exist.
    }
};
