<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Project;
use App\Models\Status;
use App\Actions\Expense\DeletedReceipt;
use App\Actions\Expense\UploadedReceipt;

new class extends Component {

  public Invoice $invoice;

  #[Rule('string|required')]
  public $number;

  #[Rule('string|required')]
  public $date;

  #[Rule('string|nullable')]
  public $title;

  #[Rule('integer|required')]
  public $status_id;

  #[Rule('date|nullable')]
  public $paid_at;

  #[Rule('string|nullable')]
  public $cancellation_reason;

  public function mount()
  {
    $this->number = $this->invoice->number;
    $this->date = $this->invoice->date->format('Y-m-d');
    $this->title = $this->invoice->title;
    $this->status_id = $this->invoice->status_id;
    $this->paid_at = $this->invoice->paid_at ? $this->invoice->paid_at->format('Y-m-d') : '';
    $this->cancellation_reason = $this->invoice->cancellation_reason;
  }

  public function edit()
  {
    $this->modal('invoice-edit')->show();
  }

  public function showUpdateStatusModal()
  {
    $this->modal('invoice-status')->show();
  }

  public function updateStatus()
  {
    $this->invoice->update([
      'status_id' => $this->status_id,
      'paid_at' => $this->paid_at !== '' ? $this->paid_at : null,
    ]);

    $this->modal('invoice-status')->close();
    $this->dispatch('invoice_updated');
  }

  public function update()
  {
    $this->validate();

    $this->invoice->update([
      'date' => $this->date,
      'title' => $this->title,
    ]);

    $this->modal('invoice-edit')->close();
    Flux::toast('Invoice updated', variant: 'success');
  }
  
  public function remove()
  {
    $this->modal('invoice-remove')->show();
  }

  public function restore($id)
  {
    $invoice = Invoice::withTrashed()->find($id);
    $invoice->update([
      'cancellation_reason' => null,
    ]);
    $invoice->restore();
    $this->invoice->refresh();
  }

  #[Computed]
  public function companies()
  {
    return Company::has('activeProjects')->orderBy('name')->get();
  }

  #[Computed]
  public function projects()
  {
    return Project::active()->orderBy('name')->get();
  }

  #[Computed]
  public function statuses()
  {
    return Status::orderBy('name')->get();
  }
};
?>
<flux:row>

  <flux:cell variant="strong">
    {{ $invoice->title }}
    <flux:badge variant="pill" size="sm" color="zinc" inset="top bottom" class="ml-2">
      {{ $invoice->company->acronym }}
    </flux:badge>
  </flux:cell>

  <flux:cell class="w-24">
    {{ $invoice->number }}
  </flux:cell>

  <flux:cell class="w-36">
    <div class="flex justify-between items-center">
      <flux:badge inset="top right" size="sm" class="text-zinc-300">
        CHF
      </flux:badge>
      {{ number_format($invoice->total, 2, '.', '') }}
    </div>
  </flux:cell>

  <flux:cell class="w-32">
    @if ($invoice->deleted_at)
      <flux:badge variant="pill" size="sm" class="uppercase" inset="top bottom" color="red">
        Cancelled
      </flux:badge>
    @else
      <flux:badge wire:click="showUpdateStatusModal" variant="pill" size="sm" class="cursor-pointer uppercase" inset="top bottom" :color="$invoice->status->color">
        {{ $invoice->status->label }}
      </flux:badge>
    @endif
  </flux:cell>

  <flux:cell class="flex justify-end">

    <flux:dropdown align="end">
      <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
      <flux:menu class="min-w-32">
        <flux:menu.item icon="pencil-square" wire:click="edit">Edit</flux:menu.item>
        <flux:menu.item icon="document-arrow-down" href="{{ route('pdf.invoice', $invoice) }}" target="_blank">PDF</flux:menu.item>
        @if ($invoice->deleted_at)
          <flux:menu.item icon="arrow-path" wire:click="restore({{ $invoice->id }})">Restore</flux:menu.item>
        @else
          <flux:menu.item icon="trash" variant="danger" wire:click="remove">Remove</flux:menu.item>
        @endif
      </flux:menu>
    </flux:dropdown>

    <flux:modal name="invoice-remove" class="min-w-[22rem] space-y-6">
      <form class="space-y-6" wire:submit="$parent.remove({{ $invoice->id }}, '{{ $cancellation_reason }}')">
        <div>
          <flux:heading size="lg">Remove invoice?</flux:heading>
          <flux:subheading class="mb-4">
            <p>You're about to delete this invoice.</p>
            <p>This action cannot be reversed.</p>
          </flux:subheading>
          <flux:textarea rows="2" label="Cancellation reason" wire:change="$set('cancellation_reason', $event.target.value)" />
        </div>
        <div class="flex justify-between gap-2">
          <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
          </flux:modal.close>
          <flux:button type="submit" variant="danger">Delete invoice</flux:button>
        </div>
      </form>
    </flux:modal>

    <flux:modal name="invoice-status" class="min-w-[22rem] space-y-6">
      <form wire:submit="updateStatus()" class="space-y-6">
        <div>
          <flux:heading size="lg">Status</flux:heading>
          <flux:subheading>Update the status of the invoice.</flux:subheading>
        </div>

        <flux:select wire:change="$set('status_id', $event.target.value)" placeholder="Choose status...">
          @foreach ($this->statuses as $status)
            <option value="{{ $status->id }}" {{ $status->id === $this->status_id ? 'selected' : '' }}>{{ \Str::upper($status->label) }}</option>
          @endforeach
        </flux:select>

        @if ($status_id == 3)
          <flux:input label="Paid at" type="date" wire:model="paid_at" />
        @endif

        <flux:button type="submit" class="w-full !mt-8" variant="primary">Update status</flux:button>

      </form>
    </flux:modal>

    <flux:modal name="invoice-edit" variant="flyout">
      <form wire:submit="update" class="space-y-6">
        <div>
          <flux:heading size="lg">Edit Invoice</flux:heading>
          <flux:subheading>Edit the invoice details.</flux:subheading>
        </div>
        <flux:input label="Date" type="date" wire:model="date" />
        <flux:input label="Title" wire:model="title" />
        {{-- <flux:textarea label="Description" rows="auto" wire:model="description" />
        <flux:input label="Amount" wire:model="amount" /> --}}
        {{-- <div class="relative space-y-6">
          <flux:select label="Currency" wire:model="currency_id" placeholder="Choose currency...">
            @foreach ($this->currencies as $currency)
              <option value="{{ $currency->id }}" {{ $currency->id === $this->currency_id ? 'selected' : '' }}>{{ $currency->label }}</option>
            @endforeach
          </flux:select>
        </div> --}}
        <flux:button type="submit" class="w-full !mt-8" variant="primary">Update Invoice</flux:button>
      </form>
    </flux:modal>
    
  </flux:cell>

</flux:row>

