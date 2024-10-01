<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Company;

new class extends Component {

  use WithPagination;

  public $sortBy = 'name';

  public $sortDirection = 'asc';

  public $search = null;

  public function updatedSearch()
  {
    $this->gotoPage(1);
  }

  public function resetSearch()
  {
    $this->search = null;
  }

  public function sort($column)
  {
    if ($this->sortBy === $column)
    {
      $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } 
    else
    {
      $this->sortBy = $column;
      $this->sortDirection = 'asc';
    }
  }

  public function remove($id)
  {
    Company::find($id)->delete();
    $this->gotoPage(1);
    $this->search = null;
  }

  #[Computed]
  #[On('company_created')]
  public function companies()
  {
    return Company::query()
      ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
      ->when($this->search !== null, fn ($query) => $query->where('name', 'like', '%'. $this->search .'%')
        ->orWhere('acronym', 'like', '%'. $this->search .'%')
        ->orWhere('city', 'like', '%'. $this->search .'%'))
      ->paginate(15);
  }

};
?>

<div class="max-w-5xl">

  <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:justify-between md:items-center">
    <div>
      <flux:heading size="xl">Companies</flux:heading>
      <flux:subheading>List of your companies</flux:subheading>
    </div>
    <div class="flex gap-x-6">
      <flux:input size="sm" wire:model.live.debounce.300ms="search" placeholder="Search...">
        <x-slot name="iconTrailing">
          <flux:button size="xs" variant="subtle" icon="x-mark" inset="right" wire:click="resetSearch" />
        </x-slot>
      </flux:input>
      <flux:modal.trigger name="company-create">
        <flux:button size="sm" icon="squares-plus">Create Company</flux:button>
      </flux:modal.trigger>
      </div>
  </div>

  <flux:table class="mt-6" :paginate="$this->companies">
    <flux:columns>
      <flux:column class="pl-2" sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:column>
      <flux:column sortable :sorted="$sortBy === 'acronym'" :direction="$sortDirection" wire:click="sort('acronym')">Acronym</flux:column>
      <flux:column sortable :sorted="$sortBy === 'city'" :direction="$sortDirection" wire:click="sort('city')">City</flux:column>
    </flux:columns>
    <flux:rows>
      @foreach ($this->companies as $company)
        <livewire:companies.company :company="$company" :key="$company->id" />
      @endforeach
    </flux:rows>
  </flux:table>

  <flux:modal name="company-create" variant="flyout">
    <livewire:widgets.create-company />
  </flux:modal>

</div>

