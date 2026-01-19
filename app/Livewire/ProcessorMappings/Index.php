<?php

namespace App\Livewire\ProcessorMappings;

use App\Models\ProcessorMapping;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Url(as: 'processor', except: '')]
    public ?string $processor = null;

    #[Url(as: 'is_active', except: '')]
    public ?string $is_active = null;

    #[Url(as: 'sort', except: '')]
    public string $sortField = 'priority';

    #[Url(as: 'direction', except: '')]
    public string $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingProcessor(): void
    {
        $this->resetPage();
    }

    public function updatingIsActive(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function delete(int $id): void
    {
        ProcessorMapping::where('id', $id)->delete();
        session()->flash('success', 'Processor mapping deleted successfully.');
    }

    public function render()
    {
        $processors = ProcessorMapping::distinct()->pluck('processor')->sort()->values();

        $mappings = ProcessorMapping::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query->where('source_field', 'like', "%{$this->search}%")
                        ->orWhere('target_field', 'like', "%{$this->search}%")
                        ->orWhere('processor', 'like', "%{$this->search}%");
                });
            })
            ->when($this->processor, function (Builder $query) {
                $query->where('processor', $this->processor);
            })
            ->when($this->is_active !== null && $this->is_active !== '', function (Builder $query) {
                $query->where('is_active', $this->is_active === '1');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.processor-mappings.index', [
            'mappings' => $mappings,
            'processors' => $processors,
        ]);
    }
}
