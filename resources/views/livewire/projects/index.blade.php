<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Company;

new class extends Component {

  use WithPagination;

  public $companies;

  public $project;

  public $sortBy = 'created_at';

  public $sortDirection = 'desc';

  #[Rule('string|required')]
  public $name;

  #[Rule('string|nullable')]
  public $budget;

  #[Rule('boolean|required')]
  public $is_collection;

  #[Rule('required')]
  public $company_id;

  #[Rule('required')]
  public $principal_id;
  
  public $search = null;

  public function mount()
  {
    $this->get();
    $this->getCompanies();
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
    $this->projects = Project::query()
      ->with('company', 'principal')
      ->when($this->search !== null, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhereHas('company', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('principal', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
        })
      ->orderBy($this->sortBy, $this->sortDirection)
      ->paginate(15);
  }

  #[On('company_stored')]
  public function getCompanies()
  {
    $this->modal('company-create')->close();
    $this->companies = Company::orderBy('name')->get();
  }

  public function save()
  {
    $this->validate();
    Project::create([
      'name' => $this->name,
      'budget' => $this->budget,
      'is_collection' => $this->is_collection,
      'company_id' => $this->company_id,
      'principal_id' => $this->principal_id,
    ]);
    $this->reset('name', 'description', 'budget', 'is_collection', 'company_id', 'principal_id');
    $this->modal('project-create')->close();
    $this->get();
  }

  public function remove($id)
  {
    Project::find($id)->delete();
    $this->get();
  }

  #[Computed]
  public function projects()
  {
    return Project::query()
        ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
        ->paginate(15);
  }
  
}; ?>


<div class="max-w-5xl">

  <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:justify-between md:items-center">
    <div>
      <flux:heading size="xl">Projects</flux:heading>
      <flux:subheading>List of your projects</flux:subheading>
    </div>
    <div class="flex gap-x-6">
      <flux:input size="sm" wire:model.live.debounce.300ms="search" placeholder="Search...">
        <x-slot name="iconTrailing">
          <flux:button size="xs" variant="subtle" inset="right" icon="x-mark" wire:click="resetSearch" />
        </x-slot>
      </flux:input>
      <flux:modal.trigger name="project-create">
        <flux:button size="sm" icon="squares-plus">Create Project</flux:button>
      </flux:modal.trigger>
      </div>
  </div>

  <flux:table class="mt-6" :paginate="$this->projects">
    <flux:columns>
      <flux:column>Name</flux:column>
      <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Year</flux:column>
      <flux:column></flux:column>
    </flux:columns>
    <flux:rows>
      @foreach ($this->projects as $project)
        <livewire:projects.project :project="$project" :key="$project->id" />
      @endforeach
    </flux:rows>
  </flux:table>

  <flux:modal name="project-create" variant="flyout" class="max-w-md">
    <form wire:submit="save" class="space-y-6">
      <div>
        <flux:heading size="lg">Create Project</flux:heading>
        <flux:subheading>Add a new project to your list.</flux:subheading>
      </div>
      <flux:input label="Name" wire:model="name" />
      <flux:input label="Budget" wire:model="budget" description="Enter the project budget." />
      <div class="my-10">
        <flux:switch wire:model="is_collection" label="Collection?" description="Collections are billed hourly." />
      </div>
      <div class="relative space-y-6">
        <flux:modal.trigger name="company-create">
          <div class="absolute top-0 right-0 mb-3">
            <flux:button size="sm" icon="squares-plus" variant="ghost" inset="top bottom">Create Company</flux:button>
          </div>
        </flux:modal.trigger>
        <flux:select label="Company" wire:model="company_id" placeholder="Choose company...">
          @foreach ($companies as $company)
            <option value="{{ $company->id }}">{{ $company->name }}</option>
          @endforeach
        </flux:select>
        <flux:select label="Principal" wire:model="principal_id" placeholder="Choose principal...">
          @foreach ($companies as $company)
            <option value="{{ $company->id }}">{{ $company->name }}</option>
          @endforeach
        </flux:select>
      </div>
      <div class="flex">
        <flux:spacer />
        <flux:button type="submit" variant="primary">Save Project</flux:button>
      </div>
    </form>
  </flux:modal>

  <flux:modal name="company-create" variant="flyout">
    <livewire:widgets.create-company />
  </flux:modal>

</div>

