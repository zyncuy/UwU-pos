<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'invoice')) {
                $table->string('invoice')->nullable()->after('id');
            }
            if (!Schema::hasColumn('transactions', 'total_price')) {
                $table->bigInteger('total_price')->default(0)->after('invoice');
            }
            if (!Schema::hasColumn('transactions', 'pay_amount')) {
                $table->bigInteger('pay_amount')->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('transactions', 'change_amount')) {
                $table->bigInteger('change_amount')->default(0)->after('pay_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['invoice', 'total_price', 'pay_amount', 'change_amount']);
        });
    }
};