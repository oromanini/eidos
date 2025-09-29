<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use ProtoneMedia\Splade\SpladeTable;

class UserController extends Controller
{
    public function index(): View
    {
        $users = SpladeTable::for(User::class)
            ->withGlobalSearch(columns: ['name', 'email'])
            ->defaultSort('created_at')
            ->column('name', 'Nome', sortable: true)
            ->column('email', 'Email', sortable: true)
            ->column('created_at', 'Data de Registro', sortable: true)
            ->column('status', 'Status')
            ->column('actions', 'Ações');

        return view('admin.users.index', compact('users'));
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['approved_at' => now()]);

        return back()->with('success', 'Usuário aprovado com sucesso!');
    }
}
