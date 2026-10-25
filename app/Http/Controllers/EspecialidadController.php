<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EspecialidadController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('especialidades.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'descripcion' => ['nullable', 'string', 'max:200'],
        ]);

        $nombreNormalizado = Str::upper(
            Str::ascii(Str::squish($datos['nombre']))
        );

        $yaExiste = Especialidad::where(
            'nombre_normalizado',
            $nombreNormalizado
        )->exists();

        if ($yaExiste) {
            return back()
                ->withErrors([
                    'nombre' => 'Ya existe una especialidad con ese nombre.',
                ])
                ->withInput();
        }

        $especialidad = new Especialidad();
        $especialidad->nombre = Str::squish($datos['nombre']);
        $especialidad->nombre_normalizado = $nombreNormalizado;
        $especialidad->descripcion = $datos['descripcion'] ?? null;
        $especialidad->estado = $request->boolean('estado', true);
        $especialidad->save();

        return redirect()
            ->route('especialidades.index')
            ->with('success', 'Especialidad creada correctamente.');
    }

    public function show(string $id)
    {
        $especialidad = Especialidad::findOrFail($id);

        return view('especialidades.show', compact('especialidad'));
    }

    public function edit(string $id)
    {
        $especialidad = Especialidad::findOrFail($id);

        return view('especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, string $id)
    {
        $especialidad = Especialidad::findOrFail($id);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'descripcion' => ['nullable', 'string', 'max:200'],
        ]);

        $nombreNormalizado = Str::upper(
            Str::ascii(Str::squish($datos['nombre']))
        );

        $yaExiste = Especialidad::where(
            'nombre_normalizado',
            $nombreNormalizado
        )
            ->where('id_especialidad', '!=', $especialidad->id_especialidad)
            ->exists();

        if ($yaExiste) {
            return back()
                ->withErrors([
                    'nombre' => 'Ya existe una especialidad con ese nombre.',
                ])
                ->withInput();
        }

        $especialidad->nombre = Str::squish($datos['nombre']);
        $especialidad->nombre_normalizado = $nombreNormalizado;
        $especialidad->descripcion = $datos['descripcion'] ?? null;

        if ($request->has('estado')) {
            $especialidad->estado = $request->boolean('estado');
        }

        $especialidad->save();

        return redirect()
            ->route('especialidades.index')
            ->with('success', 'Especialidad actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        $especialidad = Especialidad::findOrFail($id);
        $especialidad->estado = false;
        $especialidad->save();

        return redirect()
            ->route('especialidades.index')
            ->with('success', 'Especialidad desactivada correctamente.');
    }
}