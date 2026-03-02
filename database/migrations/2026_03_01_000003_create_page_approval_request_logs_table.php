<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageApprovalRequestLogsTable extends Migration
{
    public function up()
    {
        Schema::create('page_approval_request_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('action_by');
            $table->string('action', 50);
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30)->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index('request_id');
            $table->index('action_by');
            $table->index('action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('page_approval_request_logs');
    }
}
