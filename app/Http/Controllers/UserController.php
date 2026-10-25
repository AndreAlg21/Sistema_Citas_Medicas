<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('role')
            ->orderBy('nombre_completo')
            ->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'id_rol' => ['required', 'exists:roles,id_rol'],
            'nombre_completo' => ['required', 'string', 'max:120'],
            'nombre_usuario' => ['required', 'string', 'max:40', 'unique:usuarios,nombre_usuario'],
            'correo' => ['required', 'email', 'max:120', 'unique:usuarios,correo'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'estado' => ['sometimes', 'boolean'],
        ]);

        User::create($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'El usuario se registró correctamente.');
    }

    public function show(string $id)
    {
        $usuario = User::with('role')->findOrFail($id);

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(string $id)
    {
        $usuario = User::findOrFail($id);

        $roles = Role::where('estado', true)
            ->orWhere('id_rol', $usuario->id_rol)
            ->orderBy('nombre')
            ->get();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id);

        $datos = $request->validate([
            'id_rol' => ['required', 'exists:roles,id_rol'],
            'nombre_completo' => ['required', 'string', 'max:120'],
            'nombre_usuario' => [
                'required',
                'string',
                'max:40',
                Rule::unique('usuarios', 'nombre_usuario')
                    ->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'correo' => [
                'required',
                'email',
                'max:120',
                Rule::unique('usuarios', 'correo')
                    ->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'contrasena' => ['nullable', 'string', 'min:8', 'confirmed'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'estado' => ['sometimes', 'boolean'],
        ]);

        if (empty($datos['contrasena'])) {
            unset($datos['contrasena']);
        }

        $usuario->update($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'El usuario se actualizó correctamente.');
    }

    public function destroy(string $id)
    {
        $usuario = User::findOrFail($id);

        if ((int) $usuario->id_usuario === (int) auth()->id()) {
            return back()->withErrors([
                'usuario' => 'No puedes desactivar tu propia cuenta.',
            ]);
        }

        $usuario->update(['estado' => false]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'El usuario se desactivó correctamente.');
    }
}