<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use App\Models\Company;

new class extends Component {

  public $companies;

  public $company;

  public $sortBy = 'name';

  public $sortDirection = 'asc';

  public $search = null;

  public function mount()
  {
    $this->get();
  }

  public function updatedSearch()
  {
    $this->get();
  }

  public function resetSearch()
  {
    $this->search = null;
    $this->get();
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
    $this->get();
  }

  public function get()
  {
    $this->companies = Company::query()
      ->when($this->search !== null, fn ($query) => $query->where('name', 'like', '%'. $this->search .'%'))
      ->orderBy($this->sortBy, $this->sortDirection)
      ->get();
  }

  public function remove($id)
  {
    Company::find($id)->delete();
    $this->get();
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
          <flux:button size="xs" variant="subtle" inset="right" icon="x-mark" wire:click="resetSearch" />
        </x-slot>
      </flux:input>
      <flux:modal.trigger name="company-create">
        <flux:button size="sm" icon="squares-plus">Create Company</flux:button>
      </flux:modal.trigger>
      </div>
  </div>

  <flux:table class="mt-6">
    <flux:columns>
      <flux:column class="pl-2" sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:column>
      <flux:column sortable :sorted="$sortBy === 'acronym'" :direction="$sortDirection" wire:click="sort('acronym')">Acronym</flux:column>
      <flux:column sortable :sorted="$sortBy === 'city'" :direction="$sortDirection" wire:click="sort('city')">City</flux:column>
    </flux:columns>
    <flux:rows>
      @foreach ($companies as $company)
        <livewire:companies.company :company="$company" :key="$company->id" />
      @endforeach
    </flux:rows>
  </flux:table>

  <flux:modal name="company-create" variant="flyout">
    <livewire:widgets.create-company />
  </flux:modal>

</div>

