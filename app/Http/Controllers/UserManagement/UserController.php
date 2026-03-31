<?php
namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Activitylog\Models\Activity;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')
            ->where('id', '!=', auth()->id()); 

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if (auth()->user()->hasRole('admin')) {
            $query->role('sales');
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = $this->availableRoles();

        return view('user-management.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = $this->availableRoles();
        return view('user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:' . implode(',', $this->availableRoles()),
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        // Log activity
        activity()->causedBy(auth()->user())
            ->performedOn($user)
            ->log("Menambahkan user baru: {$user->name} sebagai {$request->role}");

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = $this->availableRoles();
        return view('user-management.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:' . implode(',', $this->availableRoles()),
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        $user->syncRoles($request->role);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        activity()->causedBy(auth()->user())
            ->performedOn($user)
            ->log("Menghapus user: {$user->name}");

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // Toggle aktif / nonaktif
    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        activity()->causedBy(auth()->user())
            ->performedOn($user)
            ->log("Akun {$user->name} {$status}");

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    // Reset password oleh admin
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        activity()->causedBy(auth()->user())
            ->performedOn($user)
            ->log("Reset password akun: {$user->name}");

        return back()->with('success', 'Password berhasil direset.');
    }

    // Role yang boleh dibuat sesuai role yang login
    private function availableRoles(): array
    {
        if (auth()->user()->hasRole('superadmin')) {
            return ['superadmin', 'admin', 'sales'];
        }

        return ['sales']; // admin hanya bisa buat sales
    }

    public function activityLog(Request $request)
    {
        $logs = Activity::with(['causer', 'subject'])
            ->latest()
            ->paginate(20);

        return view('user-management.activity-log', compact('logs'));
    }
}