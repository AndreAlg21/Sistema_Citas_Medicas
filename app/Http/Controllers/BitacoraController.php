<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitacoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'usuario' => ['nullable', 'string', 'max:120'],
            'accion' => ['nullable', 'string', 'max:50'],
            'ip' => ['nullable', 'ip'],
        ]);

        $bitacoras = Bitacora::query()
            ->with('usuario')
            ->when($filtros['usuario'] ?? null, fn ($consulta, $usuario) => $consulta->whereHas(
                'usuario',
                fn ($usuarios) => $usuarios->whereRaw('LOWER(nombre_completo) LIKE ?', ['%' . mb_strtolower($usuario) . '%'])
                    ->orWhereRaw('LOWER(nombre_usuario) LIKE ?', ['%' . mb_strtolower($usuario) . '%'])
            ))
            ->when($filtros['accion'] ?? null, fn ($consulta, $accion) => $consulta->whereRaw('LOWER(accion) LIKE ?', ['%' . mb_strtolower($accion) . '%']))
            ->when($filtros['ip'] ?? null, fn ($consulta, $ip) => $consulta->where('direccion_ip', $ip))
            ->orderByDesc('fecha_hora')
            ->paginate(25)
            ->withQueryString();

        return view('bitacora.index', compact('bitacoras', 'filtros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Bitacora $bitacora)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bitacora $bitacora)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bitacora $bitacora)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bitacora $bitacora)
    {
        //
    }
}
