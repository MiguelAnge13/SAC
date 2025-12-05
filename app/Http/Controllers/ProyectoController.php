<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;            // ya lo tienes
use App\Models\ProyectoImagen;      // <-- agrega esta línea
use App\Models\User;
use App\Models\Codigo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF; // si usas PDF



class ProyectoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // index: lista proyectos + pasar estudiantes y codigos
    public function index()
    {
        $proyectos = Proyecto::orderBy('created_at','desc')->get();
        // Obtener estudiantes para el select (tipo == 'estudiante')
        $estudiantes = User::where('tipo','estudiante')->get();
        $codigos = Codigo::orderBy('titulo')->get();
        return view('proyectos.index', compact('proyectos','estudiantes','codigos'));
    }

    // store: crear nuevo proyecto con integrantes e imagenes
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_hora' => 'nullable|date',
            'codigo_id' => 'nullable|exists:codigos,id',
            'integrantes' => 'nullable|array',
            'integrantes.*' => 'exists:usuarios,id',
            'imagenes.*' => 'nullable|image|max:5120', // 5MB max por imagen
            'estatus' => 'required|in:pendiente,en_progreso,completado'
        ]);

        $data = $request->only(['nombre','descripcion','fecha_hora','codigo_id','estatus']);
        $data['user_id'] = Auth::id();

        $proyecto = Proyecto::create($data);

        // asociados integrantes (many-to-many)
        if ($request->filled('integrantes')) {
            $proyecto->integrantes()->sync($request->input('integrantes'));
        }

        // imagenes
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $img) {
                $path = $img->store('proyectos', 'public'); // storage/app/public/proyectos
                ProyectoImagen::create([
                    'proyecto_id' => $proyecto->id,
                    'ruta' => $path,
                    'nombre_original' => $img->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('proyectos.index')->with('success','Reporte de proyecto creado correctamente.');
    }

    // devuelve JSON para cargar en formulario editar
    public function show($id)
    {
        $proyecto = Proyecto::with(['integrantes','imagenes','codigo'])->findOrFail($id);
        return response()->json($proyecto);
    }

    // update
    public function update(Request $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_hora' => 'nullable|date',
            'codigo_id' => 'nullable|exists:codigos,id',
            'integrantes' => 'nullable|array',
            'integrantes.*' => 'exists:usuarios,id',
            'imagenes.*' => 'nullable|image|max:5120',
            'estatus' => 'required|in:pendiente,en_progreso,completado'
        ]);

        $proyecto->update($request->only(['nombre','descripcion','fecha_hora','codigo_id','estatus']));

        if ($request->filled('integrantes')) {
            $proyecto->integrantes()->sync($request->input('integrantes'));
        } else {
            $proyecto->integrantes()->sync([]);
        }

        // agregar nuevas imágenes si las hay
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $img) {
                $path = $img->store('proyectos', 'public');
                ProyectoImagen::create([
                    'proyecto_id' => $proyecto->id,
                    'ruta' => $path,
                    'nombre_original' => $img->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('proyectos.index')->with('success','Proyecto actualizado correctamente.');
    }

    // eliminar (solo admin)
    public function destroy($id)
    {
        $user = Auth::user();
        $usuarioActual = Auth::user();
        if (! ($usuarioActual instanceof User) || ! $usuarioActual->esAdministrador()) {
            return back()->with('error','Solo administradores pueden eliminar proyectos.');
        }

        $proyecto = Proyecto::findOrFail($id);

        // eliminar imágenes físicas
        foreach ($proyecto->imagenes as $img) {
            Storage::disk('public')->delete($img->ruta);
        }

        $proyecto->delete();

        return redirect()->route('proyectos.index')->with('success','Proyecto eliminado.');
    }

    // Generar PDF del reporte (usa barryvdh/laravel-dompdf)

public function descargarPdf($id)
    {
        $proyecto = Proyecto::with(['integrantes','imagenes','codigo'])->findOrFail($id);

        // Usamos PDF (alias importado arriba)
        $pdf = PDF::loadView('proyectos.pdf', compact('proyecto'));
        $nombre = 'reporte_proyecto_' . $proyecto->id . '.pdf';

        return $pdf->download($nombre);
    }

}
