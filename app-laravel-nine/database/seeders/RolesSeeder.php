<?php
namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
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
                Role::updateOrCreate(
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
                'name' => 'Medical Representative',
                'slug' => 'medical-representative',
            ],
            [
                'name' => 'Area Manager',
                'slug' => 'area-manager',
            ],
            [
                'name' => 'Sales Manager',
                'slug' => 'sales-manager',
            ],
            [
                'name' => 'Management',
                'slug' => 'management',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
            ],
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
            ]
        ]);
    }
}
