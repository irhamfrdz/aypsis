<?php

namespace App\Http\Controllers;

use App\Models\AdmsCommand;
use App\Models\Mesin;
use App\Models\MesinUser;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MesinUserExport;

class MesinUserController extends Controller
{
    public function index(Request $request)
    {
        $query = MesinUser::query();
        
        if ($request->has('sn') && $request->sn != '') {
            $query->where('sn', $request->sn);
        }

        $users = $query->orderBy('sn')->orderBy('pin')->paginate(50);
        $mesins = Mesin::where('tipe_mesin', 'ADMS')->get();

        return view('master-mesin.users', compact('users', 'mesins'));
    }

    public function queueSync(Request $request)
    {
        $request->validate([
            'sn' => 'required'
        ]);

        $sn = $request->sn;

        // Cek apakah sudah ada antrean pending
        $existing = AdmsCommand::where('sn', $sn)
            ->where('command', 'DATA QUERY USERINFO')
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('error', 'Perintah tarik data user sudah ada di antrean untuk mesin ini.');
        }

        AdmsCommand::create([
            'sn' => $sn,
            'command' => 'DATA QUERY USERINFO',
            'status' => 'pending'
        ]);

        return back()->with('success', 'Perintah tarik data berhasil masuk antrean. Data akan masuk perlahan setelah mesin melakukan ping ke server (1-2 menit).');
    }

    public function export(Request $request)
    {
        return Excel::download(new MesinUserExport($request->sn), 'Data_User_Mesin_ADMS.xlsx');
    }
}
