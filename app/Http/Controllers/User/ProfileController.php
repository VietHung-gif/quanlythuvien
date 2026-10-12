<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Xử lý upload avatar mới nếu có (ưu tiên file upload hoặc ảnh đã cắt Base64)
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ trong storage nếu tồn tại
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu avatar mới vào storage/app/public/avatars
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
            $user->avatar = $avatarPath;
        } elseif ($request->filled('avatar_base64') && str_starts_with($request->input('avatar_base64'), 'data:image')) {
            // Xóa avatar cũ trong storage nếu tồn tại
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $base64Data = $request->input('avatar_base64');
            $parts = explode(',', $base64Data);
            if (count($parts) === 2) {
                $decoded = base64_decode($parts[1]);
                if ($decoded !== false) {
                    $avatarPath = 'avatars/avatar_'.$user->id.'_'.time().'.jpg';
                    Storage::disk('public')->put($avatarPath, $decoded);
                    $validated['avatar'] = $avatarPath;
                    $user->avatar = $avatarPath;
                }
            }
        }

        unset($validated['avatar_base64']);

        $user->fill($validated);

        if (array_key_exists('phone', $validated)) {
            if (Schema::hasColumn('users', 'phone')) {
                $user->phone = $validated['phone'];
            }
            if (Schema::hasColumn('users', 'phone_number')) {
                $user->phone_number = $validated['phone'];
            }
        }

        if (array_key_exists('address', $validated)) {
            if (Schema::hasColumn('users', 'address')) {
                $user->address = $validated['address'];
            }
            if (Schema::hasColumn('users', 'delivery_address')) {
                $user->delivery_address = $validated['address'];
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('success', 'Cập nhật thông tin hồ sơ và ảnh đại diện thành công!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
