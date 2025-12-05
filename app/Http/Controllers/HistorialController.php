<?php

namespace App\Http\Controllers;

use App\Models\EventLog;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    // middleware para proteger la vista (solo usuarios autenticados; ajusta roles si hace falta)
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = EventLog::with('user')->orderByDesc('created_at');

        // filtros simples por acción, entidad, usuario, fecha-range
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $events = $query->paginate(25)->withQueryString();

        return view('historial.index', compact('events'));
    }
}
