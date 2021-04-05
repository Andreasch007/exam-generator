<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(RolesAndPermissionsSeeder::class);


        $user = User::create([
            'name'          => 'Andreas',
            'email'         => 'tjhinandreasch007@gmail.com',
            'password'      => bcrypt('12345678'),
            'created_at'    => date("Y-m-d H:i:s"),
            'approved'      => '0'
        ]);
        $user->assignRole('Super Admin');

        $company = Company::create([
            'name'      =>  'CV. Metamorphz',
            'user_id'   => $user->id,
            'approved'  => 1,
        ]);

        $user = User::where('id','=',$user->id)
        ->update([
            'company_id'    => $company->id,
        ]);
    }
}
