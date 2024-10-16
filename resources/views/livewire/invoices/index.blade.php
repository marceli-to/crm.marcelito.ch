<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Invoice;

new class extends Component {

  use WithPagination;

  public $sortBy = 'status_id';

  public $sortDirection = 'asc';

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
      ->whereIn('status_id', [1, 2])
      ->with('company', 'project', 'status')
      ->orderBy('date', 'desc')
      ->paginate(15);
  }

  #[Computed]
  public function sumOpenInvoices()
  {
    return Invoice::query()->open()->sum('total');
  }

  #[Computed]
  public function sumPendingInvoices()
  {
    return Invoice::query()->pending()->sum('total');
  }

  #[Computed]
  public function sumPaidInvoices()
  {
    return Invoice::query()->paid()->sum('total');
  }

  #[Computed]
  public function sumInvoices()
  {
    return Invoice::query()->notCancelled()->sum('total');
  }

};
?>

<div class="max-w-5xl">
  <flux:heading size="lg">Invoice statistics</flux:heading>
  <div class="mt-4 grid grid-cols-12 gap-4"> 
    <flux:card class="col-span-6 sm:col-span-3 !p-3 flex justify-between items-center">
      <div>
        <flux:heading size="lg" class="!mb-0">
          {!! number_format($this->sumOpenInvoices, 2, '.', '&thinsp;') !!}
        </flux:heading>
        <flux:subheading class="!text-zinc-400">
          Open
        </flux:subheading>
      </div>
      <div class="bg-blue-50 p-2 rounded-lg">
        <flux:icon.currency-dollar class="text-blue-400 size-6" />
      </div>
    </flux:card>
    <flux:card class="col-span-6 sm:col-span-3 !p-3 flex justify-between items-center">
      <div>
        <flux:heading size="lg" class="!mb-0">
          {!! number_format($this->sumPendingInvoices, 2, '.', '&thinsp;') !!}
        </flux:heading>
        <flux:subheading>
          Pending
        </flux:subheading>
      </div>
      <div class="bg-amber-50 p-2 rounded-lg">
        <flux:icon.currency-dollar class="text-amber-400 size-6" />
      </div>
    </flux:card>
    <flux:card class="col-span-6 sm:col-span-3 !p-3 flex justify-between items-center">
      <div>
        <flux:heading size="lg" class="!mb-0">
          {!! number_format($this->sumPaidInvoices, 2, '.', '&thinsp;') !!}
        </flux:heading>
        <flux:subheading>
          Paid
        </flux:subheading>
      </div>
      <div class="bg-green-50 p-2 rounded-lg">
        <flux:icon.currency-dollar class="text-green-400 size-6" />
      </div>
    </flux:card>
    <flux:card class="col-span-6 sm:col-span-3 !p-3 flex justify-between items-center">
      <div>
        <flux:heading size="lg" class="!mb-0">
          {!! number_format($this->sumInvoices, 2, '.', '&thinsp;') !!}
        </flux:heading>
        <flux:subheading>
          All
        </flux:subheading>
      </div>
      <div class="bg-green-50 p-2 rounded-lg">
        <flux:icon.currency-dollar class="text-green-400 size-6" />
      </div>
    </flux:card>
  </div>

  <div class="mt-12 flex flex-col md:flex-row space-y-6 md:space-y-0 md:justify-between md:items-center">
    <div>
      <flux:heading size="lg">Invoice list</flux:heading>
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
      <flux:column>Title</flux:column>
      <flux:column sortable :sorted="$sortBy === 'number'" :direction="$sortDirection" wire:click="sort('number')">Number</flux:column>
      <flux:column sortable :sorted="$sortBy === 'grand_total'" :direction="$sortDirection" wire:click="sort('grand_total')">Total</flux:column>
      <flux:column>Status</flux:column>
    </flux:columns>
    <flux:rows>
      @foreach ($this->invoices as $invoice)
        <livewire:invoices.invoice :invoice="$invoice" :key="$invoice->id" />
      @endforeach
    </flux:rows>
  </flux:table>

</div>

