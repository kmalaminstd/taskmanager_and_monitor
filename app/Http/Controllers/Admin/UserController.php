<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,supervisor,assignee'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }


    public function show(User $user)
    {
        // Assigned tasks stats
        $assignedQuery = \App\Models\TaskAssignment::where('assignee_id', $user->id);

        $assignedStats = [
            'total' => (clone $assignedQuery)->count(),
            'pending' => (clone $assignedQuery)->where('status', 'pending')->where('is_active', true)->count(),
            'in_progress' => (clone $assignedQuery)->where('status', 'in_progress')->where('is_active', true)->count(),
            'submitted' => (clone $assignedQuery)->where('status', 'submitted')->where('is_active', true)->count(),
            'completed' => (clone $assignedQuery)->where('status', 'completed')->count(),
            'returned' => (clone $assignedQuery)->where('status', 'returned')->where('is_active', true)->count(),
            'overdue' => (clone $assignedQuery)->where('is_active', true)
                ->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
                ->where('deadline', '<', now())->count(),
            'cancelled' => (clone $assignedQuery)->where('status', 'cancelled')->count(),
        ];

        // Personal tasks stats
        $personalQuery = \App\Models\PersonalTask::where('user_id', $user->id);
        $personalStats = [
            'total' => (clone $personalQuery)->count(),
            'pending' => (clone $personalQuery)->where('status', 'pending')->count(),
            'in_progress' => (clone $personalQuery)->where('status', 'in_progress')->count(),
            'completed' => (clone $personalQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $personalQuery)->where('status', 'cancelled')->count(),
        ];

        // Recent assigned tasks
        $assignedTasks = \App\Models\TaskAssignment::with(['task.category', 'assignedBy'])
            ->where('assignee_id', $user->id)
            ->orderByDesc('assigned_at')
            ->paginate(10, ['*'], 'assigned_page');

        // Personal tasks
        $personalTasks = \App\Models\PersonalTask::where('user_id', $user->id)
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
            ->orderBy('deadline')
            ->paginate(10, ['*'], 'personal_page');

        return view('admin.users.show', compact(
            'user', 'assignedStats', 'personalStats', 'assignedTasks', 'personalTasks'
        ));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,supervisor,assignee'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->status = $data['status'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete(); // soft delete

        return back()->with('success', 'User deleted.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate yourself.');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', 'User status updated.');
    }
}
