<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['super admin', 'manager', 'employee']);
            $table->integer('employee_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });


        User::create(['email' => 'superadmin@gmail.com', 'password' => Hash::make('superadmin123'), 'role' => 'super admin', 'created_at' => now(), 'updated_at' => now()]);        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
