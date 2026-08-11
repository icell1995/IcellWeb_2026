<?php

namespace Database\Seeders;

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
        // $this->call(RoleSeeder::class);
        // $this->call(PermissionsRoleSeeder::class);
        // $this->call(UserSeeder::class);
        // $this->call(RefGroupSeeder::class);
        // $this->call(RefSeeder::class);
        // $this->call(PoldaSeeder::class);
        // $this->call(PolresSeeder::class);
        // $this->call(OfficerSeeder::class);

        // $this->call(Personals\GendersTableSeeder::class);
        // $this->call(Personals\MaritalStatusesTableSeeder::class);
        // $this->call(Personals\ReligionsTableSeeder::class);
        // $this->call(Personals\EducationsTableSeeder::class);
        // $this->call(Personals\IdentificationTypesTableSeeder::class);
        // $this->call(Personals\JobsTableSeeder::class);

        $this->callOpt();
        $this->callLib();

        // $this->callMigration();
    }

    public function callLib()
    {
        $this->call(Lib\CaseClassificationsTableSeeder::class);
        $this->call(Lib\CaseDegreeTypesTableSeeder::class);
        $this->call(Lib\CaseKeywordsTableSeeder::class);
        $this->call(Lib\CourtsTableSeeder::class);
        $this->call(Lib\CrimeClassesTableSeeder::class);
        $this->call(Lib\CrimeTypesTableSeeder::class);
        $this->call(Lib\CrimeConstitutionsTableSeeder::class);
        $this->call(Lib\DocumentCategoriesTableSeeder::class);
        $this->call(Lib\DocumentClassificationsTableSeeder::class);
        $this->call(Lib\EducationsTableSeeder::class);
        $this->call(Lib\EmploymentTypesTableSeeder::class);
        $this->call(Lib\EthnicsTableSeeder::class);
        $this->call(Lib\GendersTableSeeder::class);
        $this->call(Lib\IdentityTypesTableSeeder::class);
        $this->call(Lib\JobsTableSeeder::class);
        $this->call(Lib\LocationsTableSeeder::class);
        $this->call(Lib\MaritalStatusesTableSeeder::class);
        $this->call(Lib\PoliceDiktukEducationsTableSeeder::class);
        $this->call(Lib\PolicesTableSeeder::class);
        $this->call(Lib\PositionsTableSeeder::class);
        $this->call(Lib\ProsecutorsTableSeeder::class);
        $this->call(Lib\RanksTableSeeder::class);
        $this->call(Lib\ReligionsTableSeeder::class);
        $this->call(Lib\RolesTableSeeder::class);
        $this->call(Lib\SuspectSourcesTableSeeder::class);
        $this->call(Lib\TimezonesTableSeeder::class);
    }

    public function callOpt()
    {
        $this->call(Opt\GroupsTableSeeder::class);
        $this->call(Opt\PositionClustersTableSeeder::class);
        $this->call(Opt\StatusesTableSeeder::class);
    }
    
    public function callMigration()
    {

    }
}
