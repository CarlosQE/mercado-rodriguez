<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavePagoRequest;
use App\Models\Comerciante;
use App\Models\Obligacion;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $pagos = Pago::with('comerciante')
            ->withCount('obligaciones')
            ->when($request->buscar, function ($q, $b) {
                $q->where(function ($q) use ($b) {
                    $q->where('comprobante', 'ILIKE', "%{$b}%")
                      ->orWhereHas('comerciante', function ($c) use ($b) {
                          $c->where('nombres', 'ILIKE', "%{$b}%")
                            ->orWhere('apellidos', 'ILIKE', "%{$b}%")
                            ->orWhere('documento', 'ILIKE', "%{$b}%");
                      });
                });
            })
            ->when($request->metodo, fn ($q, $m) => $q->where('metodo', $m))
            ->orderByDesc('fecha_pago')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pagos.index', compact('pagos'));
    }

    public function create(Request $request)
    {
        $comerciantes = Comerciante::whereHas('asignaciones.obligaciones', fn ($q) => $q->where('estado', 'pendiente'))
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $comerciante = null;
        $pendientes = collect();

        if ($request->filled('comerciante_id')) {
            $comerciante = Comerciante::find($request->comerciante_id);

            if ($comerciante) {
                $pendientes = Obligacion::with('asignacion.puesto')
                    ->where('estado', 'pendiente')
                    ->whereHas('asignacion', fn ($q) => $q->where('comerciante_id', $comerciante->id))
                    ->orderBy('periodo')
                    ->get();
            }
        }

        return view('pagos.create', compact('comerciantes', 'comerciante', 'pendientes'));
    }

    public function store(SavePagoRequest $request)
    {
        $pago = DB::transaction(function () use ($request) {
            // Serializa la numeración de comprobantes entre pagos simultáneos.
            DB::select('select pg_advisory_xact_lock(?)', [7001]);

            $ids = $request->validated('obligaciones');

            $obligaciones = Obligacion::whereIn('id', $ids)
                ->where('estado', 'pendiente')
                ->lockForUpdate()
                ->get();

            if ($obligaciones->count() !== count($ids)) {
                throw ValidationException::withMessages([
                    'obligaciones' => 'Alguna obligación ya fue pagada. Recarga e intenta de nuevo.',
                ]);
            }

            $ultimo = Pago::where('comprobante', 'like', 'MR-%')->max('comprobante');
            $numero = $ultimo ? ((int) substr($ultimo, 3)) + 1 : 1;

            $pago = Pago::create([
                'comerciante_id' => $request->validated('comerciante_id'),
                'comprobante'    => sprintf('MR-%05d', $numero),
                'fecha_pago'     => $request->validated('fecha_pago'),
                'metodo'         => $request->validated('metodo'),
                'total'          => round($obligaciones->sum('importe'), 2),
            ]);

            $pago->obligaciones()->attach(
                $obligaciones->mapWithKeys(fn ($o) => [$o->id => ['monto' => $o->importe]])->all()
            );

            Obligacion::whereIn('id', $ids)->update(['estado' => 'pagada']);

            return $pago;
        });

        return redirect()->route('pagos.show', $pago)->with('ok', 'Pago registrado: ' . $pago->comprobante . '.');
    }

    public function show(Pago $pago)
    {
        $pago->load('comerciante', 'obligaciones.asignacion.puesto');

        return view('pagos.show', compact('pago'));
    }
}