<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Expense;
use App\Models\Currency;

new class extends Component {

  use WithPagination;

  public $sortBy = 'date';

  public $sortDirection = 'desc';

  public $search = null;

  #[Rule('date|required')]
  public $date = null;

  #[Rule('string|required')]
  public $title = null;

  #[Rule('string|nullable')]
  public $description = null;

  #[Rule('required')]
  public $amount = null;

  #[Rule('required')] 
  public $currency_id = null;

  #[Rule('required')]
  public $receipt = null;

  public function mount()
  {
    $this->currency_id = Currency::first()->id;
  }

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

  public function save()
  {
    $this->validate();

    $expense = Expense::create([
      'date' => $this->date,
      'title' => $this->title,
      'description' => $this->description,
      'amount' => $this->amount,
      'currency_id' => $this->currency_id,
    ]);

    // Set the expense number
    $expense->number = date('y', time()) . '.' . str_pad($expense->id, 4, "0", STR_PAD_LEFT);
    $expense->save();

    // Handle file upload
    if ($this->receipt)
    {
      foreach ($this->receipt as $receipt)
      {
        $filename = $expense->number . '.' . $receipt['extension'];
        Storage::putFileAs('public/expenses', new File($receipt['path']), $filename);
        $expense->receipt = $filename;
        $expense->save();
      }
    }

    $this->reset('date', 'title', 'description', 'amount', 'currency_id', 'receipt');
    $this->modal('expense-create')->close();
    Flux::toast('Expense created.');
  }

  public function remove($id)
  {
    Expense::find($id)->delete();
    $this->gotoPage(1);
    $this->search = null;
  }

  #[Computed]
  #[On('expense_created')]
  public function expenses()
  {
    return Expense::query()
      ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
      ->when($this->search !== null, fn ($query) => $query->where('number', 'like', '%'. $this->search .'%')
        ->orWhere('title', 'like', '%'. $this->search .'%')
        ->orWhere('description', 'like', '%'. $this->search .'%'))
      ->paginate(15);
  }

  #[Computed]
  public function currencies()
  {
    return Currency::orderBy('label')->get();
  }

};
?>

<div class="max-w-5xl">

  <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:justify-between md:items-center">
    <div>
      <flux:heading size="xl">Expenses</flux:heading>
      <flux:subheading>List of your expenses</flux:subheading>
    </div>
    <div class="flex gap-x-6">
      <flux:input size="sm" wire:model.live.debounce.300ms="search" placeholder="Search...">
        <x-slot name="iconTrailing">
          <flux:button size="xs" variant="subtle" icon="x-mark" inset="right" wire:click="resetSearch" />
        </x-slot>
      </flux:input>
      <flux:modal.trigger name="expense-create">
        <flux:button size="sm" icon="squares-plus">Create Expense</flux:button>
      </flux:modal.trigger>
      </div>
  </div>

  <flux:table class="mt-6" :paginate="$this->expenses">
    <flux:columns>
      <flux:column class="pl-2" sortable :sorted="$sortBy === 'date'" :direction="$sortDirection" wire:click="sort('date')">Date</flux:column>
      <flux:column sortable :sorted="$sortBy === 'number'" :direction="$sortDirection" wire:click="sort('number')">Number</flux:column>
      <flux:column sortable :sorted="$sortBy === 'description'" :direction="$sortDirection" wire:click="sort('description')">Description</flux:column>
      <flux:column sortable :sorted="$sortBy === 'amount'" :direction="$sortDirection" wire:click="sort('amount')">Amount</flux:column>
    </flux:columns>
    <flux:rows>
      @foreach ($this->expenses as $expense)
        <livewire:expenses.expense :expense="$expense" :key="$expense->id" />
      @endforeach
    </flux:rows>
  </flux:table>

  <flux:modal name="expense-create" variant="flyout" class="max-w-md">
    <form wire:submit="save" class="space-y-6">
      <div>
        <flux:heading size="lg">Create Expense</flux:heading>
        <flux:subheading>Add a new expense to your list.</flux:subheading>
      </div>
      <flux:input label="Date" type="date" wire:model="date" />
      <flux:input label="Title" wire:model="title" />
      <flux:textarea rows="auto" label="Description" wire:model="description" />
      <flux:input label="Amount" wire:model="amount" />
      <div class="relative space-y-6">
        <flux:select label="Currency" wire:model="currency_id" placeholder="Choose currency...">
          @foreach ($this->currencies as $currency)
            <option value="{{ $currency->id }}" {{ $currency->id === $this->currency_id ? 'selected' : '' }}>{{ $currency->label }}</option>
          @endforeach
        </flux:select>
      </div>
      <div>
        <flux:field>
          <flux:label>Receipt</flux:label>
          <livewire:dropzone
            wire:model="receipt"
            :rules="['image','mimes:png,jpeg','max:10420']"
            :multiple="false" />
        </flux:field>
      </div>
      <div class="flex">
        <flux:spacer />
        <flux:button type="submit" variant="primary">Save Expense</flux:button>
      </div>
    </form>
  </flux:modal>

</div>

