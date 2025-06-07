<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $email = env('DEFAULT_ADMIN_EMAIL', 'defaultadmin@example.com');
        $password = env('DEFAULT_ADMIN_PASSWORD', 'password');
        $name = 'Admin';

        // Only create if not exists
        if (!DB::table('users')->where('email', $email)->exists()) {
            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => UserRole::Admin->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $email = env('DEFAULT_ADMIN_EMAIL', 'defaultadmin@example.com');
        DB::table('users')->where('email', $email)->delete();
    }
};
