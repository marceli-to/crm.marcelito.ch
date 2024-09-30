<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Project;

new class extends Component {

  use WithPagination;

  public $projects;

  public function mount()
  {
    $this->projects = Project::orderBy('created_at', 'desc')->get();
  }
};
?>

<div class="max-w-5xl">

  <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:justify-between md:items-center">
    <div>
      <flux:heading size="xl">Timer</flux:heading>
      <flux:subheading>Keep track of your daily tasks</flux:subheading>
    </div>
    <div class="flex gap-x-6">
      <flux:modal.trigger name="entry-create">
        <flux:button size="sm" icon="squares-plus">Create Entry</flux:button>
      </flux:modal.trigger>
    </div>
  </div>

  {{-- <flux:table class="mt-6" :paginate="$this->entries">
    <flux:columns>
      <flux:column>Description</flux:column>
      <flux:column>Project</flux:column>
      <flux:column>Duration</flux:column>
    </flux:columns>
    <flux:rows>
      @foreach ($this->entries as $entry)
      @endforeach
    </flux:rows>
  </flux:table> --}}

  <flux:modal name="entry-create" variant="flyout" class="max-w-md">
    <form wire:submit="save" class="space-y-6">
      <div>
        <flux:heading size="lg">Create Entry</flux:heading>
        <flux:subheading>Track a new task.</flux:subheading>
      </div>
      <flux:input label="Description" wire:model="description" />
      <flux:input label="Date" wire:model="date" type="date" />
      <div class="flex justify-between gap-x-6">
        <flux:input label="Start" wire:model="time_start" type="time" class="w-44" />
        <flux:input label="End" wire:model="time_end" type="time" class="w-44" />
      </div>
      <flux:select label="Project" wire:model="project_id" placeholder="Choose project...">
        @foreach ($projects as $project)
          <option value="{{ $project->id }}">{{ $project->name }}</option>
        @endforeach
      </flux:select>
      <div class="flex">
        <flux:spacer />
        <flux:button type="submit" variant="primary">Save entry</flux:button>
      </div>
    </form>
  </flux:modal>

</div>
