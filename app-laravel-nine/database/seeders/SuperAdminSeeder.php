<?php
namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
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
            $emailSuperAdmin = config('custom-config.super_admin.email');
            $userSuperAdmin = User::whereEmail($emailSuperAdmin)->first();
            $roleSuperAdmin = Role::whereSlug('super-admin')->first();
            $userSuperAdmin->roles()->sync($roleSuperAdmin);

            $countFeatureAbilites = $roleSuperAdmin->features()->count();

            // Fill abilities
            Feature::query()->where('is_active', true)->get()->each(function(Feature $feature) use ($roleSuperAdmin, $countFeatureAbilites){
                $featureAbilities = collect($feature->actions)->flatMap(function($item){
                    return [$item=> true];
                });
                
                if (!$countFeatureAbilites) {
                    $roleSuperAdmin->features()->attach($feature, [
                        'abilities'=> $featureAbilities->toArray()
                    ]);
                }

                if ($countFeatureAbilites) {
                    $roleSuperAdmin->features()->updateExistingPivot($feature, [
                        'abilities'=> $featureAbilities->toArray()
                    ]);
                }
                
            });

            DB::commit();


        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
