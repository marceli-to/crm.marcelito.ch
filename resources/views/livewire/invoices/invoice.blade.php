<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Project;
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

  #[Rule('string|nullable')]
  public $description;

  #[Rule('required')]
  public $currency_id;

  #[Rule('string|required')]
  public $amount;

  public $receipt;

  public function mount()
  {
    $this->number = $this->invoice->number;
    $this->date = $this->invoice->date->format('Y-m-d');
    $this->title = $this->invoice->title;
  }

  public function edit()
  {
    $this->modal('invoice-edit')->show();
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
    <flux:badge variant="pill" size="sm" class="uppercase" inset="top bottom" :color="$invoice->status->color">
      {{ $invoice->status->label }}
    </flux:badge>
  </flux:cell>

  <flux:cell class="flex justify-end">
    <flux:dropdown align="end">
      <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
      <flux:menu class="min-w-32">
        <flux:menu.item icon="pencil-square" wire:click="edit">Edit</flux:menu.item>
        <flux:menu.item icon="document-arrow-down" href="{{ route('pdf.invoice', $invoice) }}" target="_blank">PDF</flux:menu.item>
        <flux:menu.item icon="trash" variant="danger" wire:click="remove">Remove</flux:menu.item>
      </flux:menu>
    </flux:dropdown>

    <flux:modal name="invoice-remove" class="min-w-[22rem] space-y-6">
      <form class="space-y-6" wire:submit="$parent.remove({{ $invoice->id }})">
        <div>
          <flux:heading size="lg">Remove invoice?</flux:heading>
          <flux:subheading>
            <p>You're about to delete this invoice.</p>
            <p>This action cannot be reversed.</p>
          </flux:subheading>
        </div>
        <div class="flex justify-between gap-2">
          <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
          </flux:modal.close>
          <flux:button type="submit" variant="danger">Delete invoice</flux:button>
        </div>
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
        <flux:textarea label="Description" rows="auto" wire:model="description" />
        <flux:input label="Amount" wire:model="amount" />
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

