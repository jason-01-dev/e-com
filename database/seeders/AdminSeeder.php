<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $admin = User::firstOrNew(
            ['email' => 'ariel243tshibangu@gmail.com']
        );

        $admin->name = 'ariel';
        $admin->password = Hash::make('0819584002'); 
        $admin->email_verified_at = now();
        $admin->remember_token = Str::random(10);

         
        if (Schema::hasColumn('users', 'is_admin')) {
            $admin->is_admin = true; 
        }

        $admin->save();

        $this->command->info('Compte admin créé ou mis à jour avec succès.');
    }
}
