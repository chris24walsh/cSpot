<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDefaultAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $email = '';
        // request the email address from the user
        echo "\nWe need to define the first user in the database who then will be the Administrator.\n";
        while ($email == '') {
            $email = readline('Enter the email address of the administrator: ');
        }
        DB::table('users')->insert(['first_name'=>'Administrator', 'email' => $email]);
        echo "\nYou can modify these values later and should first reset the password for Administrator!\n\n";

        // set some default values
        DB::table('role_user')->insert(['user_id'=>1, 'role_id'=>'1']);
        DB::table('role_user')->insert(['user_id'=>1, 'role_id'=>'2']);
        DB::table('role_user')->insert(['user_id'=>1, 'role_id'=>'3']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
