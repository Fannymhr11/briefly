<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\ActivityHistory;
use App\Models\User;
use App\Support\Stats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $f = Stats::filters($request);
        $status = $request->query('active');

        $users = User::query()->withCount(['briefs', 'histories'])
            ->when($f['q'] ?? null, function ($q, $t) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $t).'%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('role', 'like', $like));
            })
            ->when($f['role'] ?? null, fn ($q, $v) => $q->where('role', $v))
            ->when(in_array($status, ['1', '0'], true), fn ($q) => $q->where('is_active', $status === '1'))
            ->orderBy('id')->paginate(10)->withQueryString();

        $count = fn (?string $role = null) => User::query()->when($role, fn ($q) => $q->where('role', $role))->count();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $f + ['active' => $status],
            'counts' => [
                'total' => [$count(), Stats::delta(User::query())],
                'admin' => [$count('admin'), Stats::delta(User::where('role', 'admin'))],
                'marketing' => [$count('marketing'), Stats::delta(User::where('role', 'marketing'))],
                'creative' => [$count('creative'), Stats::delta(User::where('role', 'creative'))],
                'user' => [$count('user'), Stats::delta(User::where('role', 'user'))],
            ],
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => $data['password'], // di-hash otomatis oleh cast
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityHistory::log('user_created', sprintf('User baru %s ditambahkan oleh %s', $user->name, $request->user()->name), $request->user());

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $isSelf = $user->id === $request->user()->id;

        $role = $isSelf ? $user->role : $data['role'];
        $active = $isSelf ? true : $request->boolean('is_active');

        if ($this->isLastActiveAdmin($user) && ($role !== 'admin' || ! $active)) {
            return back()->withInput()->with('toast_error', 'Tidak dapat mengubah satu-satunya Admin aktif.');
        }

        $payload = ['name' => $data['name'], 'email' => $data['email'], 'role' => $role, 'is_active' => $active];
        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }
        $user->update($payload);

        ActivityHistory::log('user_updated', sprintf('Data user %s diperbarui oleh %s', $user->name, $request->user()->name), $request->user());

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('toast_error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        if ($this->isLastActiveAdmin($user)) {
            return back()->with('toast_error', 'Tidak dapat menghapus satu-satunya Admin aktif.');
        }

        // Hapus file PDF milik user agar tidak menjadi file yatim.
        $user->briefs()->get()->each(fn ($b) => Storage::disk('local')->delete($b->file_path));
        $name = $user->name;
        $user->delete();

        ActivityHistory::log('user_deleted', sprintf('User %s dihapus oleh %s', $name, $request->user()->name), $request->user());

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    private function isLastActiveAdmin(User $user): bool
    {
        return $user->role === 'admin' && $user->is_active
            && User::where('role', 'admin')->where('is_active', true)->count() <= 1;
    }
}
