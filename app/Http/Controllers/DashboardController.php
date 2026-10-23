<?php

namespace App\Http\Controllers;

use App\Models\Obligacion;
use App\Models\Pago;
use App\Models\Puesto;
use App\Models\Seccion;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $conteo = Puesto::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $ocupados = (int) ($conteo['ocupado'] ?? 0);
        $libres = (int) ($conteo['libre'] ?? 0);
        $clausurados = (int) ($conteo['clausurado'] ?? 0);
        $totalPuestos = $ocupados + $libres + $clausurados;
        $porcentajeOcupacion = $totalPuestos > 0 ? round($ocupados / $totalPuestos * 100, 1) : 0;

        $recaudadoMes = (float) Pago::whereBetween('fecha_pago', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString(),
        ])->sum('total');

        $pendienteTotal = (float) Obligacion::where('estado', 'pendiente')->sum('importe');

        $vencidas = Obligacion::where('estado', 'pendiente')
            ->whereDate('fecha_vencimiento', '<', today())
            ->selectRaw('count(*) as cantidad, coalesce(sum(importe), 0) as monto')
            ->first();

        $vencidasCantidad = (int) $vencidas->cantidad;
        $vencidasMonto = (float) $vencidas->monto;

        $secciones = Seccion::withCount([
            'puestos',
            'puestos as ocupados_count' => fn ($q) => $q->where('estado', 'ocupado'),
        ])->orderBy('nombre')->get();

        $desde = now()->startOfMonth()->subMonths(5);
        $recaudacion = DB::table('pagos')
            ->selectRaw('extract(year from fecha_pago) as anio, extract(month from fecha_pago) as mes, sum(total) as total')
            ->where('fecha_pago', '>=', $desde->toDateString())
            ->where('fecha_pago', '<=', now()->endOfMonth()->toDateString())
            ->groupBy('anio', 'mes')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get()
            ->keyBy(fn ($fila) => sprintf('%04d-%02d', $fila->anio, $fila->mes));

        $meses = collect(range(5, 0))->map(function ($i) use ($desde, $recaudacion) {
            $fecha = $desde->copy()->addMonths($i);
            $clave = $fecha->format('Y-m');

            return [
                'clave' => $clave,
                'etiqueta' => ucfirst($fecha->translatedFormat('M Y')),
                'total' => (float) ($recaudacion[$clave]->total ?? 0),
            ];
        });

        $deudores = DB::table('comerciantes')
            ->join('asignaciones', 'asignaciones.comerciante_id', '=', 'comerciantes.id')
            ->join('obligaciones', 'obligaciones.asignacion_id', '=', 'asignaciones.id')
            ->where('obligaciones.estado', 'pendiente')
            ->groupBy('comerciantes.id', 'comerciantes.nombres', 'comerciantes.apellidos', 'comerciantes.documento')
            ->orderByDesc('deuda')
            ->limit(5)
            ->get([
                'comerciantes.id',
                'comerciantes.nombres',
                'comerciantes.apellidos',
                'comerciantes.documento',
                DB::raw('sum(obligaciones.importe) as deuda'),
            ]);

        $porVencer = Obligacion::with(['asignacion.puesto', 'asignacion.comerciante'])
            ->where('estado', 'pendiente')
            ->whereBetween('fecha_vencimiento', [today()->toDateString(), today()->addDays(7)->toDateString()])
            ->orderBy('fecha_vencimiento')
            ->limit(10)
            ->get();

        $ultimosPagos = Pago::with('comerciante')
            ->orderByDesc('fecha_pago')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $hayAlerta = $vencidasCantidad > 0 || $clausurados > 0;

        return view('dashboard.index', compact(
            'ocupados',
            'libres',
            'clausurados',
            'totalPuestos',
            'porcentajeOcupacion',
            'recaudadoMes',
            'pendienteTotal',
            'vencidasCantidad',
            'vencidasMonto',
            'secciones',
            'meses',
            'deudores',
            'porVencer',
            'ultimosPagos',
            'hayAlerta',
        ));
    }
}
