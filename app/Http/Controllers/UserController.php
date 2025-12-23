<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminStoreRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function __construct(private AdminService $adminService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        [
            'admins' => $admins,
            'filters' => $filters,
            'totalAdmins' => $totalAdmins,
            'activeAdmins' => $activeAdmins,
            'pendingAdmins' => $pendingAdmins,
        ] = $this->adminService->getIndexData((int) Auth::id());

        return view('admin.index', compact('admins', 'filters', 'totalAdmins', 'activeAdmins', 'pendingAdmins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = ['Superadmin', 'Admin'];

        return view('admin.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->adminService->createAdmin($data);

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
    public function edit(User $admin): View
    {
        $roles = ['Superadmin', 'Admin'];

        return view('admin.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request, User $admin): RedirectResponse
    {
        $data = $request->validated();

        $this->adminService->updateAdmin($admin, $data);

        return redirect()->route('admin.index')->with('status', 'Admin berhasil diperbarui.');
    }

    /**
     * Export admins to CSV.
     */
    public function export(): StreamedResponse
    {
        return $this->adminService->exportAdmins();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin): RedirectResponse
    {
        if ($admin->id === Auth::id()) {
            return redirect()->route('admin.index')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $this->adminService->deleteAdmin($admin);

        return redirect()->route('admin.index')->with('status', 'Admin dihapus.');
    }
}
