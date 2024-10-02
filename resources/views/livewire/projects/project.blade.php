<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use App\Models\Project;
use App\Models\Company;
use App\Models\Rate;

new class extends Component {

  public Project $project;

  public $companies;

  public $rates;

  #[Rule('string|required')]
  public $name;

  #[Rule('string|nullable')]
  public $budget;

  #[Rule('boolean|required')]
  public $is_collection;

  #[Rule('required')]
  public $rate_id;
  
  #[Rule('required')]
  public $company_id;

  #[Rule('required')]
  public $principal_id;

  public $principal_name;

  public function mount()
  {
    $this->name = $this->project->name;
    $this->description = $this->project->description;
    $this->budget = $this->project->budget;
    $this->is_collection = $this->project->is_collection;
    $this->rate_id = $this->project->rate_id;
    $this->company_id = $this->project->company_id;
    $this->principal_id = $this->project->principal_id;
    $this->getCompanies();
    $this->getRates();
  }

  #[On('company_created')]
  public function getCompanies()
  {
    $this->modal('company-create-'. $this->project->id)->close();
    $this->companies = Company::orderBy('name')->get();
  }

  public function getRates()
  {
    $this->rates = Rate::orderBy('label')->get();
  }

  public function edit()
  {
    $this->modal('project-edit')->show();
  }

  public function update()
  {
    $this->validate();

    $this->project->update([
      'name' => $this->name,
      'budget' => $this->budget,
      'is_collection' => $this->is_collection,
      'company_id' => $this->company_id,
      'principal_id' => $this->principal_id,
    ]);
    
    Flux::toast('Your changes have been saved.');

    $this->modal('project-edit')->close();
  }

  public function archive()
  {
    $this->project->archived_at = now();
    $this->project->save();
    Flux::toast('Project archived.');
  }

  public function restore()
  {
    $this->project->archived_at = null;
    $this->project->save();
    Flux::toast('Project restored.');
  }
  
  public function remove()
  {
    $this->modal('project-remove')->show();
  }
};
?>
<flux:row>

  <flux:cell variant="strong" class="w-3/4 pr-4">
    <a href="#" wire:click.prevent="edit">
      {{ $project->name }}
      <flux:tooltip content="{{ $project->company->name }}" position="right" inset="top bottom">
        <flux:badge variant="pill" size="sm" color="zinc" inset="top bottom">
          {{ $project->company->acronym }}
        </flux:badge>
      </flux:tooltip> 
    </a>
  </flux:cell>

  <flux:cell>
    {{ $project->created_at->format('Y') }}
  </flux:cell>

  <flux:cell>
    @if ($project->archived_at)
    <div class="flex justify-end">
      <flux:badge size="sm" color="amber" inset="top bottom">Archived</flux:badge>
    </div>
    @endif
  </flux:cell>

  <flux:cell class="flex justify-end">
    <flux:dropdown align="end">
      <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
      <flux:menu class="min-w-32">
        <flux:menu.item icon="pencil-square" wire:click="edit">Edit</flux:menu.item>
        @if (!$project->archived_at)
          <flux:menu.item icon="archive-box" wire:click="archive">Archive</flux:menu.item>
        @else
          <flux:menu.item icon="arrow-path" wire:click="restore">Restore</flux:menu.item>
        @endif
        <flux:menu.item icon="trash" variant="danger" wire:click="remove">Remove</flux:menu.item>
      </flux:menu>
    </flux:dropdown>

    <flux:modal name="project-remove" class="min-w-[22rem] space-y-6">
      <form class="space-y-6" wire:submit="$parent.remove({{ $project->id }})">
        <div>
          <flux:heading size="lg">Remove project?</flux:heading>
          <flux:subheading>
            <p>You're about to delete this project.</p>
            <p>This action cannot be reversed.</p>
          </flux:subheading>
        </div>
        <div class="flex justify-between gap-2">
          <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
          </flux:modal.close>
          <flux:button type="submit" variant="danger">Delete project</flux:button>
        </div>
      </form>
    </flux:modal>

    <flux:modal name="project-edit" variant="flyout" class="max-w-md">
      <form wire:submit="update" class="space-y-6">
        
        <div>
          <flux:heading size="lg">Edit Project</flux:heading>
          <flux:subheading>Edit the project details.</flux:subheading>
        </div>
        
        <flux:input label="Name" wire:model="name" />
        
        <flux:input label="Budget" wire:model="budget" description="Enter the project budget." />
        
        <div class="my-10">
          <flux:switch wire:model.live="is_collection" label="Collection?" description="Collections are billed hourly." />
        </div>

        <flux:select label="Rate" description="Select the rate for the project." wire:model="rate_id" placeholder="Choose rate...">
          @foreach ($rates as $rate)
            <option value="{{ $rate->id }}" @if($rate->id == $rate_id) selected @endif>
              {{ number_format($rate->label, 2, '.', '') }}
            </option>
          @endforeach
        </flux:select>

        <div class="relative space-y-6 !mt-12">
          
          <flux:modal.trigger name="company-create-{{ $project->id }}">
            <div class="absolute top-0 right-0 mb-3">
              <flux:button size="sm" icon="squares-plus" variant="ghost" inset="top bottom">Create Company</flux:button>
            </div>
          </flux:modal.trigger>
          
          <flux:select label="Company" wire:model="company_id" placeholder="Choose company...">
            @foreach ($companies as $company)
              <option value="{{ $company->id }}" @if($company->id == $company_id) selected @endif>
                {{ $company->name }}
              </option>
            @endforeach
          </flux:select>

          <flux:select label="Principal" wire:model="principal_id" placeholder="Choose principal...">
            @foreach ($companies as $company)
              <option value="{{ $company->id }}" @if($company->id == $company_id) selected @endif>
                {{ $company->name }}
              </option>
            @endforeach
          </flux:select>

        </div>
        <div class="flex">
          <flux:spacer />
          <flux:button type="submit" variant="primary">Save changes</flux:button>
        </div>
      </form>
    </flux:modal>

    <flux:modal name="company-create-{{ $project->id }}" variant="flyout">
      <livewire:widgets.create-company />
    </flux:modal>
  </flux:cell>
</flux:row>