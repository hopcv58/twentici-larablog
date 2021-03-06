<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Create permissions
         */
        $crudPost = Permission::create([
            'name' => 'crud-post'
        ]);
        $updateOthersPost = Permission::create([
            'name' => 'update-others-post'
        ]);
        $deleteOthersPost = Permission::create([
            'name' => 'delete-others-post'
        ]);
        $crudCategory = Permission::create([
            'name' => 'crud-category'
        ]);
        $crudUser = Permission::create([
            'name' => 'crud-user'
        ]);
        /**
         * Attach permissions to roles
         */
        $admin = Role::whereName('admin')->first();
        $admin->attachPermissions([$crudPost, $updateOthersPost, $deleteOthersPost, $crudCategory, $crudUser]);

        $editor = Role::whereName('editor')->first();
        $editor->attachPermissions([$crudPost, $updateOthersPost, $deleteOthersPost, $crudCategory]);

        $author = Role::whereName('author')->first();
        $author->attachPermission($crudPost);
        $users = User::all();
        $users[0]->attachPermissions([$crudPost, $updateOthersPost, $deleteOthersPost, $crudCategory, $crudUser]);
        $users[1]->attachPermissions([$crudPost, $updateOthersPost, $deleteOthersPost, $crudCategory]);
        $users[2]->attachPermission($crudPost);
        for($i=3;$i<$users->count();$i++)
        {
            $users[$i]->attachPermission($crudPost);
        }
    }
}
