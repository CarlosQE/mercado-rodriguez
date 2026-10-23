<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Obligacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObligacionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'periodo' => ['nullable', 'date_format:Y-m'],
            'estado'  => ['nullable', 'in:pendiente,vencida,pagada'],
        ]);

        $query = Obligacion::with(['asignacion.puesto.seccion', 'asignacion.comerciante'])
            ->when($request->periodo, fn ($q, $p) => $q->whereDate('periodo', $p . '-01'))
            ->when($request->estado === 'pagada', fn ($q) => $q->where('estado', 'pagada'))
            ->when($request->estado === 'pendiente', fn ($q) => $q->where('estado', 'pendiente')->whereDate('fecha_vencimiento', '>=', now()->toDateString()))
            ->when($request->estado === 'vencida', fn ($q) => $q->where('estado', 'pendiente')->whereDate('fecha_vencimiento', '<', now()->toDateString()))
            ->when($request->buscar, function ($q, $b) {
                $q->where(function ($q) use ($b) {
                    $q->whereHas('asignacion.puesto', fn ($p) => $p->where('codigo', 'ILIKE', "%{$b}%"))
                    ->orWhereHas('asignacion.comerciante', function ($c) use ($b) {
                        $c->where('nombres', 'ILIKE', "%{$b}%")
                            ->orWhere('apellidos', 'ILIKE', "%{$b}%")
                            ->orWhere('documento', 'ILIKE', "%{$b}%");
                    });
                });
            });

        $pendiente = (clone $query)->where('estado', 'pendiente')->sum('importe');

        $obligaciones = $query
            ->orderByDesc('periodo')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('obligaciones.index', compact('obligaciones', 'pendiente'));
    }

    public function emitir(Request $request)
    {
        $datos = $request->validate([
            'periodo'           => ['required', 'date_format:Y-m'],
            'fecha_vencimiento' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $periodo = Carbon::createFromFormat('!Y-m', $datos['periodo'])->startOfMonth();
        $vence = $request->filled('fecha_vencimiento')
            ? Carbon::parse($datos['fecha_vencimiento'])
            : $periodo->copy()->day(10);

        if ($periodo->gt(now()->startOfMonth())) {
            return back()->withErrors(['periodo' => 'No se pueden emitir períodos futuros.'])->withInput();
        }

        if ($vence->lt($periodo)) {
            return back()->withErrors(['fecha_vencimiento' => 'El vencimiento no puede ser anterior al período.'])->withInput();
        }

        $fecha = $periodo->toDateString();

        $asignaciones = Asignacion::with('puesto.seccion')
            ->whereDate('fecha_inicio', '<=', $fecha)
            ->where(fn ($q) => $q->whereNull('fecha_fin')->orWhereDate('fecha_fin', '>', $fecha))
            ->whereNotIn('id', Obligacion::whereDate('periodo', $fecha)->select('asignacion_id'))
            ->get();

        if ($asignaciones->isEmpty()) {
            return back()->with('error', 'No hay asignaciones por facturar en ' . $periodo->translatedFormat('F Y') . ' (quizá ya estaban emitidas).');
        }

        $ahora = now();
        $filas = $asignaciones->map(fn ($a) => [
            'asignacion_id'     => $a->id,
            'periodo'           => $fecha,
            'fecha_vencimiento' => $vence->toDateString(),
            'superficie_m2'     => $a->puesto->superficie_m2,
            'tarifa_m2'         => $a->puesto->seccion->tarifa_m2,
            'importe'           => round((float) $a->puesto->superficie_m2 * (float) $a->puesto->seccion->tarifa_m2, 2),
            'estado'            => 'pendiente',
            'created_at'        => $ahora,
            'updated_at'        => $ahora,
        ])->all();

        DB::transaction(function () use ($filas) {
            foreach (array_chunk($filas, 500) as $lote) {
                Obligacion::insert($lote);
            }
        });

        return redirect()
            ->route('obligaciones.index', ['periodo' => $periodo->format('Y-m')])
            ->with('ok', count($filas) . ' obligaciones emitidas para ' . $periodo->translatedFormat('F Y') . '.');
    }
}