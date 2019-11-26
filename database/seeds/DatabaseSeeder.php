<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(DaysTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(CodesTableSeeder::class);
        $this->call(SuperintendentGroupsTableSeeder::class);
        $this->call(InstitutionsTableSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(ScheduleTableSeeder::class);
        $this->call(TimeScheduleTableSeeder::class);
        $this->call(SkipsTableSeeder::class);
    }
}
