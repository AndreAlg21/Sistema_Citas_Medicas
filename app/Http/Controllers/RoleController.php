<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['usuarios', 'permisos'])
            ->orderBy('nombre')
            ->paginate(10);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permisos = Permiso::orderBy('codigo')->get();

        return view('roles.create', compact('permisos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:40', 'unique:roles,nombre'],
            'descripcion' => ['nullable', 'string', 'max:150'],
            'estado' => ['sometimes', 'boolean'],
            'permisos' => ['sometimes', 'array'],
            'permisos.*' => ['integer', 'distinct', 'exists:permisos,id_permiso'],
        ]);

        $idsPermisos = $datos['permisos'] ?? [];
        unset($datos['permisos']);

        $role = Role::create($datos);
        $role->permisos()->sync($idsPermisos);

        return redirect()
            ->route('roles.index')
            ->with('success', 'El rol se creó correctamente.');
    }

    public function show(Role $role)
    {
        $role->load('permisos');

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $role->load('permisos');
        $permisos = Permiso::orderBy('codigo')->get();

        return view('roles.edit', compact('role', 'permisos'));
    }

    public function update(Request $request, Role $role)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:40',
                Rule::unique('roles', 'nombre')
                    ->ignore($role->id_rol, 'id_rol'),
            ],
            'descripcion' => ['nullable', 'string', 'max:150'],
            'estado' => ['sometimes', 'boolean'],
            'permisos' => ['sometimes', 'array'],
            'permisos.*' => ['integer', 'distinct', 'exists:permisos,id_permiso'],
        ]);

        if (
            $role->nombre === 'Administrador'
            && $datos['nombre'] !== 'Administrador'
        ) {
            return back()
                ->withErrors(['nombre' => 'No cambies el nombre del rol Administrador.'])
                ->withInput();
        }

        if (
            $role->nombre === 'Administrador'
            && isset($datos['estado'])
            && !(bool) $datos['estado']
        ) {
            return back()
                ->withErrors(['estado' => 'El rol Administrador no se puede desactivar.'])
                ->withInput();
        }

        if (
            isset($datos['estado'])
            && !(bool) $datos['estado']
            && $role->usuarios()->exists()
        ) {
            return back()
                ->withErrors(['estado' => 'No puedes desactivar un rol que todavía tiene usuarios.'])
                ->withInput();
        }

        $idsPermisos = $datos['permisos'] ?? [];
        unset($datos['permisos']);

        $role->update($datos);
        $role->permisos()->sync($idsPermisos);

        return redirect()
            ->route('roles.index')
            ->with('success', 'El rol se actualizó correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->nombre === 'Administrador') {
            return back()->withErrors([
                'rol' => 'El rol Administrador no se puede desactivar.',
            ]);
        }

        if ($role->usuarios()->exists()) {
            return back()->withErrors([
                'rol' => 'No puedes desactivar un rol que todavía tiene usuarios.',
            ]);
        }

        $role->update(['estado' => false]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'El rol se desactivó correctamente.');
    }
}