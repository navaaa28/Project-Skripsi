<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MapelController extends Controller
{
    public function index()
    {
        return view('mapel.index', [
            'mapel' => Mapel::latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('mapel.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_mapel' => ['required', 'string', 'max:50', 'unique:mapel,nama_mapel'],
            'kkm' => ['required', 'integer'],
        ]);

        Mapel::create($data);

        return redirect()->route('admin.mapel.index');
    }

    public function show(Mapel $mapel)
    {
        return view('mapel.show', compact('mapel'));
    }

    public function edit(Mapel $mapel)
    {
        return view('mapel.edit', compact('mapel'));
    }

    public function update(Request $request, Mapel $mapel)
    {
        $data = $request->validate([
            'nama_mapel' => ['required', 'string', 'max:50', Rule::unique('mapel', 'nama_mapel')->ignore($mapel->id_mapel, 'id_mapel')],
            'kkm' => ['required', 'integer'],
        ]);

        $mapel->update($data);

        return redirect()->route('admin.mapel.index');
    }

    public function destroy(Mapel $mapel)
    {
        $mapel->delete();

        return redirect()->route('admin.mapel.index');
    }
}
