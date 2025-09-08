<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Karyawan;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

use function PHPUnit\Framework\returnSelf;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $users = User::with('karyawan')->get();
        return view('master-user.index', compact('users'));
    }

    /**
     * Menampilkan formulir untuk membuat pengguna baru.
     *
     * @return \Illuminate\View\View
     */

    public function create()
    {
        // Mengambil semua izin yang tersedia dari tabel permission
        $permissions = Permission::select('id', 'name', 'description')->get();
        $karyawans = Karyawan::select('id', 'nama_lengkap')->get();

        // Mengambil semua users dengan permissions untuk fitur copy
        $users = User::with('permissions:id,name')->select('id', 'name', 'username')->get();

        return view('master-user.create', compact('permissions', 'karyawans', 'users'));
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'username'=>'required|string|max:255|unique:users',
            'password'=>'required|string|min:8|confirmed',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password'=> Hash::make($request->password),
            'karyawan_id' => $request->karyawan_id,
        ]);

        // Melampirkan izin yang dipilih ke pengguna
        $user->permissions()->sync($request->input('permissions',[]));

        return redirect()->route('master.user.index')->with('success', 'Pengguna berhasil ditambahkan!');

    }

    public function edit(User $user)
    {
        // Mengambil semua izin yang tersedia dan izin yang dimiliki pengguna
        $permissions = Permission::select('id', 'name', 'description')->get();
        $userPermissions = $user->permissions->pluck('id')->toArray();
        $karyawans = Karyawan::select('id', 'nama_lengkap')->get();

        // Mengambil semua users dengan permissions untuk fitur copy (kecuali user yang sedang diedit)
        $users = User::with('permissions:id,name')->select('id', 'name', 'username')->where('id', '!=', $user->id)->get();

        return view('master-user.edit', compact('user', 'permissions', 'userPermissions', 'karyawans', 'users'));

    }

    /**
     * Memperbarui data pengguna yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id', // Memvalidasi ID izin
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->karyawan_id = $request->karyawan_id;

        if($request->password){
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Memperbarui izin pengguna
        $user->permissions()->sync($request->input('permissions',[]));

        return redirect()->route('master.user.index')->with('success','Pengguna berhasil diperbarui!');


    }

    /**
     * Menghapus pengguna dari database.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy(User $user)
    {
        //Menghapus relasi izin sebelum menghapus pengguna
        $user->permissions()->detach();
        $user->delete();
        return redirect()->route('master.user.index')->with('success','Pengguna berhsail dihapus!');
    }





}
