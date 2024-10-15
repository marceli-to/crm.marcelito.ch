<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Models\Timer;
use App\Models\Company;
use App\Models\Project;

new class extends Component {

  public Timer $entry;

  public $is_not_today = false;

  #[Rule('required|boolean')]
  public $is_billable = true;

  #[Rule('required')]
  public $task;

  #[Rule('required_if:is_not_today,true')]
  public $date;

  #[Rule('required')]
  public $time_start;

  #[Rule('required')]
  public $time_end;

  #[Rule('required|exists:companies,id')]
  public $company_id;

  #[Rule('exists:projects,id')]
  public $project_id;
  
  public function mount()
  {
    $this->task = $this->entry->task;
    $this->date =  $this->entry->date->format('Y-m-d');
    $this->time_start = $this->entry->time_start->format('H:i');
    $this->time_end = $this->entry->time_end->format('H:i');
    $this->company_id = $this->entry->project->company_id;
    $this->project_id = $this->entry->project_id;
    $this->is_billable = $this->entry->is_billable;
    $this->is_not_today = $this->entry->date->format('Y-m-d') !== now()->format('Y-m-d');
  }

  public function update()
  {
    $this->validate();

    $this->entry->update([
      'task' => $this->task,
      'date' => $this->is_not_today ? $this->date : now()->format('Y-m-d'),
      'time_start' => $this->time_start,
      'time_end' => $this->time_end,
      'duration' => \Carbon\Carbon::parse($this->time_start)->diffInMinutes(\Carbon\Carbon::parse($this->time_end)),
      'project_id' => $this->project_id,
      'is_billable' => $this->is_billable,
      'is_not_today' => $this->is_not_today,
    ]);
    
    Flux::toast('Entry updated', variant: 'success');
    $this->modal('entry-edit')->close();
    $this->dispatch('entry_updated');
  }

  public function edit()
  {
    $this->modal('entry-edit')->show();
  }

  public function remove()
  {
    $this->modal('entry-remove')->show();
  }

  #[Computed]
  #[On('entry_company_changed')]
  public function projects()
  {
    return Project::where('company_id', $this->company_id)->active()->orderBy('created_at', 'desc')->get();
  }

  #[Computed]
  public function companies()
  {
    return Company::has('activeProjects')->orderBy('name')->get();
  }

};
?>

<flux:row>
  <flux:cell variant="strong" class="w-1/4">
    {{ $entry->task }}
  </flux:cell>
  <flux:cell class="w-1/2">
  <flux:badge inset="top bottom" size="sm" color="violet">
    {{ $entry->project->name }}
    <span class="text-xs text-violet-700 mx-1">&bull;</span>
    {{ $entry->project->company->acronym }}
  </flux:badge>

  </flux:cell>
  <flux:cell class="hidden md:table-cell">
    {{ $entry->time_start->format('H:i') }}
    <span class="text-xs text-zinc-500">&ndash;</span>
    {{ $entry->time_end->format('H:i') }}
  </flux:cell>
  <flux:cell class="text-right">
    {{ humanized_duration($entry->duration, true) }}
  </flux:cell>

  <flux:cell class="flex justify-end">

    <flux:dropdown align="end">
      <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
      <flux:menu class="min-w-32">
        <flux:menu.item icon="pencil-square" wire:click="edit">Edit</flux:menu.item>
        <flux:menu.item icon="trash" variant="danger" wire:click="remove">Remove</flux:menu.item>
      </flux:menu>
    </flux:dropdown>

    <flux:modal name="entry-remove" class="min-w-[22rem] space-y-6">
      <form class="space-y-6" wire:submit="$parent.remove({{ $entry->id }})">
        <div>
          <flux:heading size="lg">Remove entry?</flux:heading>
          <flux:subheading>
            <p>You're about to delete this entry.</p>
            <p>This action cannot be reversed.</p>
          </flux:subheading>
        </div>
        <div class="flex justify-between gap-2">
          <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
          </flux:modal.close>
          <flux:button type="submit" variant="danger">Delete entry</flux:button>
        </div>
      </form>
    </flux:modal>

    <flux:modal name="entry-edit" variant="flyout" class="max-w-md">
      <form wire:submit="update" class="space-y-6">
        
        <div>
          <flux:heading size="lg">Edit Entry</flux:heading>
          <flux:subheading>Edit the entry details.</flux:subheading>
        </div>
        
        <div class="my-10">
          <flux:switch wire:model="is_billable" label="Billable?" description="Exclude entries from being billed." />
        </div>
  
        <div class="my-10">
          <flux:switch wire:model.live="is_not_today" label="Not today?" description="Set entry date to a different day." />
        </div>
  
        <flux:input label="Task" wire:model="task" />
  
        <flux:select label="Company" wire:model="company_id" wire:change="dispatch('entry_company_changed')" placeholder="Choose company...">
          @foreach ($this->companies as $company)
            <option value="{{ $company->id }}">{{ $company->name }}</option>
          @endforeach
        </flux:select>
  
        <flux:select label="Project" wire:model="project_id">
          @foreach ($this->projects as $project)
            <option value="{{ $project->id }}">{{ $project->name }}</option>
          @endforeach
        </flux:select>
  
        @if ($is_not_today)
          <flux:input label="Date" wire:model="date" type="date" />
        @endif
  
        <div class="flex justify-between gap-x-6">
          <flux:input label="Start" wire:model="time_start" :value="$entry->time_start->format('H:i')" type="time" class="!w-44" />
          <flux:input label="End" wire:model="time_end" :value="$entry->time_end->format('H:i')" type="time" class="!w-44" />
        </div>
        <flux:button type="submit" class="w-full !mt-8" variant="primary">Update Entry</flux:button>
      </form>
    </flux:modal>

  </flux:cell>

</flux:row>