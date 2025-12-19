<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentId = Auth::id();

        $totalAdmins = User::count();
        $activeAdmins = User::whereNotNull('email_verified_at')->count();
        $pendingAdmins = $totalAdmins - $activeAdmins;

        $admins = User::orderByDesc('created_at')
            ->paginate(10)
            ->through(function (User $user) use ($currentId) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'Admin',
                    'status' => $user->email_verified_at ? 'Aktif' : 'Tidak aktif',
                    'created_at' => Carbon::parse($user->created_at)->locale('id')
                        ->translatedFormat('l, d F Y'),
                    'is_self' => $user->id === $currentId,
                ];
            });

        $filters = ['Semua', 'Superadmin', 'Admin'];

        return view('admin.index', compact('admins', 'filters', 'totalAdmins', 'activeAdmins', 'pendingAdmins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = ['Superadmin', 'Admin'];

        return view('admin.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['Superadmin', 'Admin'])],
            'verified' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'email_verified_at' => $request->boolean('verified') ? now() : null,
        ]);

        return redirect()->route('admin.index')->with('status', 'Admin berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $admin)
    {
        $roles = ['Superadmin', 'Admin'];

        return view('admin.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $admin)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['Superadmin', 'Admin'])],
            'verified' => ['nullable', 'boolean'],
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->role = $data['role'];
        $admin->email_verified_at = $request->boolean('verified') ? now() : null;

        if (! empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        return redirect()->route('admin.index')->with('status', 'Admin berhasil diperbarui.');
    }

    /**
     * Export admins to CSV.
     */
    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="admins.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'email', 'role', 'verified', 'created_at']);

            foreach (User::orderBy('name')->cursor() as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->role ?? 'Admin',
                    $user->email_verified_at ? 'true' : 'false',
                    optional($user->created_at)->toDateTimeString(),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        if ($admin->id === Auth::id()) {
            return redirect()->route('admin.index')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $admin->delete();

        return redirect()->route('admin.index')->with('status', 'Admin dihapus.');
    }
}
