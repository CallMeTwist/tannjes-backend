<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('status')->default('pending_payment'); // pending_payment|pending_approval|approved|rejected
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
