<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            session()->flash('error', __('You cannot delete your own account.'));

            return;
        }

        $user->delete();
        session()->flash('success', __('User deleted successfully.'));
    }

    public function render()
    {
        $this->authorize('viewAny', User::class);

        return view('livewire.users.index', [
            'users' => User::query()
                ->when($this->search !== '', function (Builder $query) {
                    $query->where(function (Builder $q) {
                        $q->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    });
                })
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
