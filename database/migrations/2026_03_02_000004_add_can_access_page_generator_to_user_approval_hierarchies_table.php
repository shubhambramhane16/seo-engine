<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanAccessPageGeneratorToUserApprovalHierarchiesTable extends Migration
{
    public function up()
    {
        Schema::table('user_approval_hierarchies', function (Blueprint $table) {
            if (!Schema::hasColumn('user_approval_hierarchies', 'can_access_page_generator')) {
                $table->boolean('can_access_page_generator')->default(0)->after('admin_id');
            }
        });
    }

    public function down()
    {
        Schema::table('user_approval_hierarchies', function (Blueprint $table) {
            if (Schema::hasColumn('user_approval_hierarchies', 'can_access_page_generator')) {
                $table->dropColumn('can_access_page_generator');
            }
        });
    }
}
