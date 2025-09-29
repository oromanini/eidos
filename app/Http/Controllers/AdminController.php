<?php

namespace App\Http\Controllers;

use App\Models\User;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\SpladeTable;
use Illuminate\Contracts\View\View; // Importe a classe View

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View // Especifique o tipo de retorno
    {
        return view('admin.users.index', [
            'users' => SpladeTable::for(User::class)
                ->withGlobalSearch(columns: ['name', 'email'])
                ->defaultSort('created_at')
                ->column('name', 'Nome', sortable: true)
                ->column('email', 'Email', sortable: true)
                ->column('created_at', 'Data de Registro', sortable: true)
                ->column('status', 'Status')
                ->column('actions', 'Ações', canBeHidden: false)
        ]);
    }

    public function approve(User $user)
    {
        $user->update(['approved_at' => now()]);

        Toast::title('Usuário aprovado!')
            ->message("O usuário {$user->name} agora pode acessar o sistema.")
            ->success()
            ->autoDismiss(5);

        return redirect()->back();
    }
}
