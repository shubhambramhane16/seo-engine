<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageApprovalRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('page_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('manager_approver_id')->nullable();
            $table->unsignedBigInteger('admin_approver_id')->nullable();
            $table->unsignedBigInteger('current_approver_id')->nullable();
            $table->json('old_payload')->nullable();
            $table->json('new_payload');
            $table->string('status', 30)->default('pending_manager');
            $table->text('approver_comments')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->unsignedBigInteger('overridden_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['page_id', 'status']);
            $table->index(['current_approver_id', 'status']);
            $table->index(['requested_by']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('page_approval_requests');
    }
}
