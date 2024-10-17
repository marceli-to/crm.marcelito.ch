<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Project;
use App\Models\Vat;

new class extends Component {

  #[Rule('date|required')]
  public $date = null;

  #[Rule('date|required')]
  public $due_at = null;

  #[Rule('string|required')]
  public $title = null;

  #[Rule('integer|required')]
  public $vat_id = null;

  #[Rule('integer|required')]
  public $company_id;

  #[Rule('integer')]
  public $project_id = null;

  public function mount()
  {
    $this->date = now()->format('Y-m-d');
    $this->due_at = now()->addDays(21)->format('Y-m-d');
  }

  public function save()
  {
    $this->validate();
    
    $invoice = Invoice::create([
      'date' => $this->date,
      'title' => $this->title,
    ]);

    Flux::toast('Invoice created', variant: 'success');
  }

  #[Computed]
  public function vats()
  {
    return Vat::all();
  }

  #[Computed]
  public function companies()
  {
    return Company::orderBy('name')->get();
  }

  #[Computed]
  public function projects()
  {
    return Project::with('company')
      ->active()
      ->orderBy('created_at', 'desc')
      ->get();
  }

  #[On('project_changed')]
  public function project_changed()
  {

  }


};
?>

<div class="max-w-2xl">

  <flux:heading size="xl">Create Invoice</flux:heading>
  <flux:subheading>Create a new invoice</flux:subheading>

  <form wire:submit="save" class="space-y-6 mt-6">
    <flux:input label="Title" wire:model="title" />

    {{-- <flux:select label="Company" variant="combobox" wire:model="company_id" placeholder="Choose company..." wire:change="dispatch('company_changed')">
      @foreach ($this->companies as $company)
        <flux:option value="{{ $company->id }}">{{ $company->name }}{{ $company->city ? ', ' . $company->city : '' }}</flux:option>
      @endforeach
    </flux:select> --}}
      <flux:select label="Project" variant="listbox" wire:model="project_id" placeholder="Choose project..." wire:change="dispatch('project_changed')">
        @foreach ($this->projects as $project)
          <flux:option value="{{ $project->id }}">
            {{ $project->company->acronym }}: {{ $project->name }}
          </flux:option>
        @endforeach
      </flux:select>

    {{ $project_id }}
    
    <div class="grid grid-cols-12 gap-x-6">
      <div class="col-span-6">
        <flux:input label="Date" type="date" wire:model="date" />
      </div>
      <div class="col-span-6">
        <flux:input label="Due at" type="date" wire:model="due_at" />
      </div>
    </div>

    <flux:button type="submit" class="w-full !mt-8" variant="primary">Store Invoice</flux:button>

  </form>

</div>

