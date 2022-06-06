<?php
namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {

            $this->collection()->each(function ($item) {
                Feature::query()->updateOrCreate(
                    \Illuminate\Support\Arr::only($item, ['slug']),
                    $item
                );
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function collection(): \Illuminate\Support\Collection
    {
        return collect([
            [
                'slug'=> 'user',
                'name'=> 'User',
                'description'=> 'Manage user in a company',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete'
                ]
            ],[
                'slug'=> 'territory',
                'name'=> 'Territory',
                'description'=> 'Manage territories of company',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete'
                ]
            ],[
                'slug'=> 'company',
                'name'=> 'Company',
                'description'=> 'Manage companies',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete'
                ]
            ],[
                'slug'=> 'employee',
                'name'=> 'Employee',
                'description'=> 'Manage employees of company',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete', 'search'
                ]
            ],[
                'slug'=> 'role',
                'name'=> 'Role',
                'description'=> 'Manage roles',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete'
                ]
            ],[
                'slug'=> 'country',
                'name'=> 'Country',
                'description'=> 'Manage countries',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'detail', 'delete', 'search'
                ]
            ],[
                'slug'=> 'region',
                'name'=> 'Region',
                'description'=> 'Manage regions',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete', 'search'
                ]
            ],[
                'slug'=> 'area',
                'name'=> 'Area',
                'description'=> 'Manage areas',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete', 'search'
                ]
            ],[
                'slug'=> 'doctor',
                'name'=> 'Doctor',
                'description'=> 'Manage doctors',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete', 'search', 'import', 'approval'
                ]
            ],[
                'slug'=> 'team',
                'name'=> 'Team',
                'description'=> 'Manage Teams',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete', 'search'
                ]
            ],[
                'slug'=> 'brand',
                'name'=> 'Brand',
                'description'=> 'Manage Brands',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete', 'search'
                ]
            ],[
                'slug'=> 'call-plan',
                'name'=> 'Call Plan',
                'description'=> 'Call Plan',
                'is_active'=> true,
                'actions'=> [
                    'view', 'create', 'edit', 'delete'
                ]
            ],
        ]);
    }
}
