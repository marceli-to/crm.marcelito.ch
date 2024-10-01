<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Company;
use App\Models\Timer;

new class extends Component {

  use WithPagination;

  public $projects;

  public $companies;

  public $is_not_today = false;

  #[Rule('required|boolean')]
  public $is_billable = true;

  #[Rule('required')]
  public $description;

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

  public function mount()
  {
    $this->companies = Company::has('activeProjects')->orderBy('name')->get();
  }

  public function save()
  {
    $this->validate();
    $entry = Timer::create([
      'description' => $this->description,
      'date' => $this->is_not_today ? $this->date : now()->format('Y-m-d'),
      'time_start' => $this->time_start,
      'time_end' => $this->time_end,
      'duration' => \Carbon\Carbon::parse($this->time_start)->diffInMinutes(\Carbon\Carbon::parse($this->time_end)),
      'is_billable' => $this->is_billable,
      'company_id' => $this->company_id,
      'project_id' => $this->project_id,
    ]);

    $this->reset('description', 'date', 'time_start', 'time_end', 'company_id', 'project_id');
    $this->modal('entry-create')->close();
  }

  public function getProjects()
  {
    $this->projects = Project::where('company_id', $this->company_id)
      ->active()
      ->orderBy('created_at', 'desc')
      ->get();

    // Set the project_id if there is only one project
    if ($this->projects->count() === 1)
    {
      $this->project_id = $this->projects->first()->id;
    }
  }

  public function getDailyTotal($entries)
  {
    $total = $entries->sum('duration');
    return [
      'color' => $total > 360 ? 'lime' : 'amber',
      'label' => floor($total / 60) . 'h ' . ($total % 60 ? ($total % 60) . 'm' : ''),
    ];
  }

  #[Computed]
  public function entries()
  {
    $entries = Timer::with('project.company')
      ->orderBy('date', 'desc')
      ->orderBy('time_start', 'desc')
      ->get();

    return $entries->groupBy('date');
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

  @foreach ($this->entries as $day => $entries)
    <flux:heading class="!mt-0 flex justify-between">
      <div>{{ date('Y-m-d', strtotime($day)) === date('Y-m-d') ? 'Today' : date('l, j.m.Y', strtotime($day)) }}</div>
      <div>
        <flux:badge size="sm" inset="top bottom" color="{{ $this->getDailyTotal($entries)['color'] }}">
        {{ $this->getDailyTotal($entries)['label'] }}
      </flux:badge></div>
    </flux:heading>
    <flux:table class="mt-2 mb-12 border-t border-t-zinc-200">
      <flux:rows>
        @foreach ($entries as $entry)
          <flux:row :key="$entry->id" class="!border-0 {{ $loop->even ? 'bg-zinc-50' : '' }}">
            <flux:cell variant="strong" class="w-1/4">
              {{ $entry->description }}
            </flux:cell>
            <flux:cell class="w-1/2">
              {{ $entry->project->name }}
              <span class="text-xs text-zinc-300 mx-1">&bull;</span>
              {{ $entry->project->company->name }}
            </flux:cell>
            <flux:cell class="hidden md:table-cell">
              {{ $entry->time_start->format('H:i') }}
              <span class="text-xs text-zinc-500">&ndash;</span>
              {{ $entry->time_end->format('H:i') }}
            </flux:cell>
            <flux:cell class="text-right">{{ $entry->humanized_duration }}</flux:cell>
          </flux:row>
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
        <flux:switch wire:model.live="is_not_today" label="Not today?" description="Create entry for a different day." />
      </div>

      <flux:input label="Description" wire:model="description" />

      <flux:select label="Company" wire:model="company_id" wire:change="getProjects" placeholder="Choose company...">
        @foreach ($companies as $company)
          <option value="{{ $company->id }}">{{ $company->name }}</option>
        @endforeach
      </flux:select>

      @if ($company_id)
        <flux:select label="Project" wire:model="project_id">
          @foreach ($projects as $project)
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
