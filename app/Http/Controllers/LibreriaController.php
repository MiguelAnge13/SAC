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
    public function index(Request $request)
{
    $q = $request->query('q');

    $librerias = Libreria::when($q, function ($builder, $qVal) {
        $builder->where(function($b) use ($qVal) {
            $b->where('nombre', 'like', '%' . $qVal . '%')
              ->orWhere('lenguaje', 'like', '%' . $qVal . '%')
              ->orWhere('version', 'like', '%' . $qVal . '%')
              ->orWhere('descripcion', 'like', '%' . $qVal . '%');
        });
    })->orderBy('nombre')->get();

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
    $filename = 'librerias_' . date('Ymd_His') . '.csv';

    $callback = function () {
        $out = fopen('php://output', 'w');

        // BOM para que Excel reconozca UTF-8 en Windows
        fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // cabecera
        fputcsv($out, ['nombre','lenguaje','version','descripcion']);

        Libreria::orderBy('nombre')->chunk(200, function($rows) use ($out) {
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->nombre,
                    $r->lenguaje,
                    $r->version,
                    $r->descripcion,
                ]);
            }
        });

        fclose($out);
    };

    return response()->streamDownload($callback, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
    ]);
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
