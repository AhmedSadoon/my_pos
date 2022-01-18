<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=User::create([
            'first_name'=>'super',
            'last_name'=>'admin',
            'email'=>'superadmin@app.com',
            'password'=>bcrypt('11111111'),

        ]);

        $user->attachRoles(['super-admin']);
    }
}
