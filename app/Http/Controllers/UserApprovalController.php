<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserApprovalController extends Controller
{
    /**
     * Display pending user registrations
     */
    public function index()
    {
        // Check if user has permission to view user approvals
        $user = Auth::user();
        $userPermissions = $user->permissions->pluck('name')->toArray();
        $hasAccess = !empty(array_intersect($userPermissions, [
            'master-user',
            'user-approval',
            'user-approval.view'
        ]));

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $pendingUsers = User::with(['karyawan', 'approvedBy'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedUsers = User::with(['karyawan', 'approvedBy'])
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->limit(20)
            ->get();

        $rejectedUsers = User::with(['karyawan', 'approvedBy'])
            ->where('status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.user-approval', compact('pendingUsers', 'approvedUsers', 'rejectedUsers'));
    }

    /**
     * Approve user registration
     */
    public function approve(Request $request, User $user)
    {
        // Check if user has permission to approve user registrations
        $currentUser = Auth::user();
        $userPermissions = $currentUser->permissions->pluck('name')->toArray();
        $hasAccess = !empty(array_intersect($userPermissions, [
            'master-user',
            'user-approval',
            'user-approval.approve'
        ]));

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui user.');
        }

        if ($user->status !== 'pending') {
            return back()->withErrors(['error' => 'User ini tidak dalam status pending.']);
        }

        $user->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', "Akun {$user->name} telah disetujui dan dapat digunakan.");
    }

    /**
     * Reject user registration
     */
    public function reject(Request $request, User $user)
    {
        // Check if user has permission to reject user registrations
        $currentUser = Auth::user();
        $userPermissions = $currentUser->permissions->pluck('name')->toArray();
        $hasAccess = !empty(array_intersect($userPermissions, [
            'master-user',
            'user-approval',
            'user-approval.approve'
        ]));

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki izin untuk menolak user.');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        if ($user->status !== 'pending') {
            return back()->withErrors(['error' => 'User ini tidak dalam status pending.']);
        }

        $user->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'registration_reason' => $user->registration_reason . "\n\nDitolak: " . ($request->rejection_reason ?? 'Tidak ada alasan yang diberikan'),
        ]);

        return back()->with('success', "Akun {$user->name} telah ditolak.");
    }

    /**
     * View user details for approval
     */
    public function show(User $user)
    {
        // Check if user has permission to view user details
        $currentUser = Auth::user();
        $userPermissions = $currentUser->permissions->pluck('name')->toArray();
        $hasAccess = !empty(array_intersect($userPermissions, [
            'master-user',
            'user-approval',
            'user-approval.view'
        ]));

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki izin untuk melihat detail user.');
        }

        $user->load(['karyawan', 'approvedBy']);
        return view('admin.user-approval-detail', compact('user'));
    }
}
