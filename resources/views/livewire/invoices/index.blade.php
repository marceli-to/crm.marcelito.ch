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

  public function remove(Invoice $invoice, $cancellation_reason)
  {
    $invoice->update([
      'status_id' => 1, 
      'cancellation_reason' => $cancellation_reason,
      'paid_at' => null,
    ]);
    $invoice->delete();
    $this->gotoPage(1);
    $this->search = null;
    Flux::toast('Invoice deleted.', variant: 'success');
  }

  #[Computed]
  #[On('invoice_updated')]
  public function invoices()
  {
    return Invoice::query()
      ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
      ->when($this->search !== null, fn ($query) => $query->where('number', 'like', '%'. $this->search .'%')
        ->orWhere('title', 'like', '%'. $this->search .'%')
        ->orWhere('text', 'like', '%'. $this->search .'%')
        ->withTrashed()
      )
      ->whereIn('status_id', [1, 2, 3])
      ->with('company', 'project', 'status')
      ->orderBy('date', 'desc')
      ->paginate(30);
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

