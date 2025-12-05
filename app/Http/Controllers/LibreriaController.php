<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Libreria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LibreriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // mostrar vista
    public function index()
    {
        // Traer todas las librerías (puedes paginar si quieres)
        $librerias = Libreria::orderBy('nombre')->get();
        return view('librerias.index', compact('librerias'));
    }

    // crear nueva librería
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'lenguaje' => 'nullable|string|max:100',
            'version' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['nombre','lenguaje','version','descripcion']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('icono')) {
            $path = $request->file('icono')->store('librerias','public');
            $data['icono'] = $path;
        }

        Libreria::create($data);

        return redirect()->route('librerias.index')->with('success','Librería guardada correctamente.');
    }

    // info JSON (para editar en frontend)
    public function show($id)
    {
        $lib = Libreria::findOrFail($id);
        return response()->json($lib);
    }

    // actualizar
    public function update(Request $request, $id)
    {
        $lib = Libreria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'lenguaje' => 'nullable|string|max:100',
            'version' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['nombre','lenguaje','version','descripcion']);

        if ($request->hasFile('icono')) {
            // eliminar icono viejo si existe
            if ($lib->icono) {
                Storage::disk('public')->delete($lib->icono);
            }
            $path = $request->file('icono')->store('librerias','public');
            $data['icono'] = $path;
        }

        $lib->update($data);

        return redirect()->route('librerias.index')->with('success','Librería actualizada.');
    }

    // eliminar (solo admin)
    public function destroy($id)
    {
        $user = Auth::user();
        $usuarioActual = Auth::user();
        if (! ($usuarioActual instanceof User) || ! $usuarioActual->esAdministrador()) {
            return back()->with('error','Solo administradores pueden eliminar librerías.');
        }

        $lib = Libreria::findOrFail($id);
        if ($lib->icono) {
            Storage::disk('public')->delete($lib->icono);
        }
        $lib->delete();

        return redirect()->route('librerias.index')->with('success','Librería eliminada.');
    }

    // Export CSV (descarga)
    public function exportCsv()
    {
        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            // cabecera CSV
            fputcsv($handle, ['nombre','lenguaje','version','descripcion']);
            Libreria::orderBy('nombre')->chunk(200, function($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [$r->nombre, $r->lenguaje, $r->version, $r->descripcion]);
                }
            });
            fclose($handle);
        });

        $response->headers->set('Content-Type','text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition','attachment; filename="librerias_export.csv"');

        return $response;
    }

    // Import CSV sencillo (columna: nombre,lenguaje,version,descripcion)
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv');
        $stream = fopen($file->getRealPath(), 'r');

        // leer cabecera y luego filas
        $header = fgetcsv($stream, 0, ',');
        while (($row = fgetcsv($stream, 0, ',')) !== false) {
            // mapear por índice: asumimos orden correcto
            $nombre = $row[0] ?? null;
            $lenguaje = $row[1] ?? null;
            $version = $row[2] ?? null;
            $descripcion = $row[3] ?? null;

            if ($nombre) {
                // evitar duplicados por nombre+lenguaje
                Libreria::updateOrCreate(
                    ['nombre' => $nombre, 'lenguaje' => $lenguaje],
                    ['version' => $version, 'descripcion' => $descripcion, 'user_id' => Auth::id()]
                );
            }
        }
        fclose($stream);

        return redirect()->route('librerias.index')->with('success','Importación completada.');
    }

    // Endpoint JSON para que otros módulos (ej. codigos) consuman
    public function apiList()
    {
        $libs = Libreria::orderBy('nombre')->get(['id','nombre','lenguaje','version','descripcion','icono']);
        // añadir url de icono
        $libs->transform(function($i){
            $i->icono_url = $i->icono ? asset('storage/'.$i->icono) : asset('logo.png');
            return $i;
        });
        return response()->json($libs);
    }
}
