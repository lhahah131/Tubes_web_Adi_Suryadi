<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get authenticated user with type hint
     */
    private function getAuthUser(): ?User
    {
        return Auth::user();
    }

    public function index()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return redirect('/login');
        }

        if ($user->role === 'guru') {
            return redirect()->route('guru.dashboard'); 
        } else {
            return redirect()->route('siswa.dashboard');
        }
    }
}
