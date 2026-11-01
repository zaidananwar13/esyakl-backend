<?php

namespace App\Services;

use App\Models\Kategori;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KategoriService
{
        public function getAllKategori(): Collection
    {
        $kategori = Kategori::select('id_class_level as id', 'level as judul')->get();

        foreach ($kategori as $k) {
            $classIds = DB::table('class')
                ->select('id_class')
                ->where('id_class_level', $k->id)
                ->pluck('id_class');

            $k->jumlah_kelas = $classIds->count();

            $k->jumlah_user = DB::table('class_student')
                ->whereIn('id_class', $classIds)
                ->count();

            $k->kelas = DB::table('class')
                ->where('id_class_level', $k->id)
                ->get();
        }

        return $kategori;
    }
}
