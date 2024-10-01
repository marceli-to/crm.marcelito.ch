<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Project;
use App\Models\Company;
use App\Models\Timer;

new class extends Component {

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

  #[Rule('exists:companies,id')]
  public $company_id;

  #[Rule('exists:projects,id')]
  public $project_id;

  public function save()
  {
    $this->validate();
    $entry = Timer::create([
      'task' => $this->task,
      'date' => $this->is_not_today ? $this->date : now()->format('Y-m-d'),
      'time_start' => $this->time_start,
      'time_end' => $this->time_end,
      'duration' => \Carbon\Carbon::parse($this->time_start)->diffInMinutes(\Carbon\Carbon::parse($this->time_end)),
      'is_billable' => $this->is_billable,
      'project_id' => $this->project_id,
    ]);

    $this->reset('task', 'date', 'time_start', 'time_end', 'company_id', 'project_id');
    $this->modal('entry-create')->close();
  }

  public function getDailyTotal($entries)
  {
    $total = $entries->sum('duration');
    return [
      'color' => $total > 360 ? 'lime' : 'red',
      'label' => floor($total / 60) . 'h ' . ($total % 60 ? ($total % 60) . 'm' : ''),
    ];
  }

  public function remove($id)
  {
    Timer::find($id)->delete();
  }

  #[Computed]
  public function entries()
  { 
    return Timer::with('project.company')
      ->orderBy('date', 'desc')
      ->orderBy('time_start', 'desc')
      ->get();
  }

  #[Computed]
  public function companies()
  {
    return Company::has('activeProjects')->orderBy('name')->get();
  }

  #[Computed]
  #[On('company_changed')]
  public function projects()
  {
    $data = Project::where('company_id', $this->company_id)
      ->active()
      ->orderBy('created_at', 'desc')
      ->get();

    // Set the project_id to the first possible project
    $this->project_id = $data->first()->id;
    return $data;
  }

};
?>

<div class="max-w-5xl">

  <div class="flex flex-col md:flex-row mb-12 space-y-6 md:space-y-0 md:justify-between md:items-center">
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

  @foreach ($this->entries->groupBy('date') as $day => $entriesByDay)
    <flux:heading class="!mt-0 flex justify-between">
      <div class="text-zinc-500">
        {{ date('Y-m-d', strtotime($day)) === date('Y-m-d') ? 'Today' : date('l, j.m.Y', strtotime($day)) }}
      </div>
      <div>
        <flux:badge size="sm" inset="top bottom" color="{{ $this->getDailyTotal($entriesByDay)['color'] }}">
          {{ $this->getDailyTotal($entriesByDay)['label'] }}
        </flux:badge>
      </div>
    </flux:heading>
    <flux:table class="mt-4 mb-12 border-y border-zinc-200">
      <flux:rows>
        @foreach ($entriesByDay as $entry)
          <livewire:timer.entry :entry="$entry" :key="$entry->id" />
        @endforeach
      </flux:rows>
    </flux:table>
  @endforeach

  <flux:modal name="entry-create" variant="flyout" class="max-w-md">
    <form wire:submit="save" class="space-y-6">

      <div>
        <flux:heading size="lg">Create Entry</flux:heading>
        <flux:subheading>Track a new task.</flux:subheading>
      </div>

      <div class="my-10">
        <flux:switch wire:model="is_billable" label="Billable?" description="Exclude entries from being billed." />
      </div>

      <div class="my-10">
        <flux:switch wire:model.live="is_not_today" label="Not today?" description="Set entry date to a different day." />
      </div>

      <flux:input label="Task" wire:model="task" />

      <flux:select label="Company" wire:model="company_id" wire:change="dispatch('company_changed')" placeholder="Choose company...">
        @foreach ($this->companies as $company)
          <option value="{{ $company->id }}">{{ $company->name }}</option>
        @endforeach
      </flux:select>

      @if ($company_id)
        <flux:select label="Project" wire:model="project_id">
          @foreach ($this->projects as $project)
            <option value="{{ $project->id }}">{{ $project->name }}</option>
          @endforeach
        </flux:select>
      @endif
        
      @if ($is_not_today)
        <flux:input label="Date" wire:model="date" type="date" />
      @endif

      <div class="flex justify-between gap-x-6">
        <flux:input label="Start" wire:model="time_start" type="time" class="!w-44" />
        <flux:input label="End" wire:model="time_end" type="time" class="!w-44" />
      </div>

      <div class="flex">
        <flux:spacer />
        <flux:button type="submit" variant="primary">Save entry</flux:button>
      </div>
    </form>
  </flux:modal>

</div>
