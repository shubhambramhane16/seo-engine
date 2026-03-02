<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserApprovalHierarchiesTable extends Migration
{
    public function up()
    {
        Schema::create('user_approval_hierarchies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('manager_id');
            $table->index('admin_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_approval_hierarchies');
    }
}
