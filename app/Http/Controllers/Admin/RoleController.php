<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(25);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::pluck('name', 'id');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles', 'permissions' => 'array']);
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);
        return redirect()->route('admin.roles.index')->with('success', 'Ролята е добавена.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::pluck('name', 'id');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|unique:roles,name,'.$role->id, 'permissions' => 'array']);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);
        return redirect()->route('admin.roles.index')->with('success', 'Ролята е обновена.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Ролята е изтрита.');
    }
}