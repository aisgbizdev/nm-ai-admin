<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminService
{
    public function getIndexData(int $currentUserId): array
    {
        $totalAdmins = User::query()->count();
        $activeAdmins = User::query()->whereNotNull('email_verified_at')->count();
        $pendingAdmins = $totalAdmins - $activeAdmins;

        $admins = User::query()
            ->orderByDesc('created_at')
            ->paginate(10)
            ->through(function (User $user) use ($currentUserId) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'Admin',
                    'status' => $user->email_verified_at ? 'Aktif' : 'Tidak aktif',
                    'created_at' => Carbon::parse($user->created_at)->locale('id')
                        ->translatedFormat('l, d F Y'),
                    'is_self' => $user->id === $currentUserId,
                ];
            });

        $filters = ['Semua', 'Superadmin', 'Admin'];

        return compact('admins', 'filters', 'totalAdmins', 'activeAdmins', 'pendingAdmins');
    }

    public function createAdmin(array $data): User
    {
        return User::query()->create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'email_verified_at' => (bool) ($data['verified'] ?? false) ? now() : null,
        ]);
    }

    public function updateAdmin(User $admin, array $data): User
    {
        $admin->name = $data['name'];
        $admin->username = $data['username'];
        $admin->email = $data['email'];
        $admin->role = $data['role'];
        $admin->email_verified_at = (bool) ($data['verified'] ?? false) ? now() : null;

        if (! empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        return $admin;
    }

    public function deleteAdmin(User $admin): void
    {
        $admin->delete();
    }

    public function exportAdmins(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="admins.csv"',
        ];

        $callback = static function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'email', 'role', 'verified', 'created_at']);

            foreach (User::query()->orderBy('name')->cursor() as $user) {
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

        return Response::stream($callback, 200, $headers);
    }
}
