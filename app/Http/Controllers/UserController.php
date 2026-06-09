<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = $request->input('role');
        $kelasFilter = $request->input('kelas');
        $q = $request->input('q');
        $kelasOptions = \App\Models\Kelas::orderBy('nama_kelas')->get();

        // ── Grouped view (role=all): group by role, siswa sub-grouped by kelas ──
        if ($roleFilter === 'all') {
            $query = User::with('siswa.kelas')->orderBy('username');
            if ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('username', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            }
            $groupedUsers = $query->get()->groupBy('role');

            // Sub-group siswa by kelas
            $siswaByKelas = null;
            if ($groupedUsers->has('siswa')) {
                $siswaByKelas = $groupedUsers['siswa']
                    ->groupBy(fn ($u) => $u->siswa?->kelas?->nama_kelas ?? 'Tanpa Kelas');
            }

            return view('users.index', [
                'users' => null,
                'groupedUsers' => $groupedUsers,
                'siswaByKelas' => $siswaByKelas,
                'selectedRole' => 'all',
                'kelasOptions' => $kelasOptions,
                'selectedKelas' => null,
            ]);
        }

        // ── Siswa role: group by kelas ──
        if ($roleFilter === 'siswa') {
            $query = User::with('siswa.kelas')->where('role', 'siswa')->orderBy('username');
            if ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('username', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            }
            if ($kelasFilter && is_numeric($kelasFilter)) {
                $query->whereHas('siswa', fn ($s) => $s->where('id_kelas', (int) $kelasFilter));
            }

            $siswaUsers = $query->get();
            $siswaByKelas = $siswaUsers->groupBy(fn ($u) => $u->siswa?->kelas?->nama_kelas ?? 'Tanpa Kelas');

            return view('users.index', [
                'users' => null,
                'groupedUsers' => null,
                'siswaByKelas' => $siswaByKelas,
                'selectedRole' => 'siswa',
                'kelasOptions' => $kelasOptions,
                'selectedKelas' => $kelasFilter,
            ]);
        }

        // ── Default / admin / guru: flat paginated view ──
        $query = User::orderBy('username');

        if ($roleFilter && in_array($roleFilter, ['admin', 'guru'])) {
            $query->where('role', $roleFilter);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        return view('users.index', [
            'users' => $query->paginate(15)->appends($request->only(['role', 'q'])),
            'groupedUsers' => null,
            'siswaByKelas' => null,
            'selectedRole' => $roleFilter,
            'kelasOptions' => $kelasOptions,
            'selectedKelas' => null,
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa'])],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id_user, 'id_user')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa'])],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index');
    }
}
