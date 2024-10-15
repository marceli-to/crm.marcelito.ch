<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Invoice;

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


  public function mount()
  {
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
    
    // $expense = (new CreateExpense())->execute([
    //   'date' => $this->date,
    //   'title' => $this->title,
    //   'description' => $this->description,
    //   'amount' => $this->amount,
    //   'currency_id' => $this->currency_id,
    // ]);

    // $this->reset('date', 'title', 'description', 'amount', 'currency_id', 'receipt');
    // $this->modal('expense-create')->close();
    // Flux::toast('Expense created', variant: 'success');
  }

  public function remove($id)
  {
    $invoice = Invoice::find($id);
    $invoice->delete();
    $this->gotoPage(1);
    $this->search = null;
    Flux::toast('Invoice deleted.');
  }

  #[Computed]
  public function invoices()
  {
    return Invoice::query()
      ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
      ->when($this->search !== null, fn ($query) => $query->where('number', 'like', '%'. $this->search .'%')
        ->orWhere('title', 'like', '%'. $this->search .'%')
        ->orWhere('text', 'like', '%'. $this->search .'%')
      )
      ->paginate(15);
  }

};
?>

<div class="max-w-5xl">

  <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:justify-between md:items-center">
    <div>
      <flux:heading size="xl">Invoices</flux:heading>
      <flux:subheading>List of your invoices</flux:subheading>
    </div>
    <div class="flex gap-x-6">
      <flux:input size="sm" wire:model.live.debounce.300ms="search" placeholder="Search...">
        <x-slot name="iconTrailing">
          <flux:button size="xs" variant="subtle" icon="x-mark" inset="right" wire:click="resetSearch" />
        </x-slot>
      </flux:input>
      <flux:button size="sm" icon="squares-plus">Create Invoice</flux:button>
      </div>
  </div>

  <flux:table class="mt-6" :paginate="$this->invoices">
    <flux:columns>
      <flux:column class="!pl-2" sortable :sorted="$sortBy === 'number'" :direction="$sortDirection" wire:click="sort('number')">Number</flux:column>
      <flux:column sortable :sorted="$sortBy === 'title'" :direction="$sortDirection" wire:click="sort('title')">Title</flux:column>
      <flux:column sortable :sorted="$sortBy === 'grand_total'" :direction="$sortDirection" wire:click="sort('grand_total')">Total</flux:column>
    </flux:columns>
    <flux:rows>
      @foreach ($this->invoices as $invoice)
        <livewire:invoices.invoice :invoice="$invoice" :key="$invoice->id" />
      @endforeach
    </flux:rows>
  </flux:table>

</div>

