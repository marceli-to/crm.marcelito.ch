<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use App\Models\Expense;
use App\Models\Currency;

new class extends Component {

  public Expense $expense;

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

  public function mount()
  {
    $this->number = $this->expense->number;
    $this->date = $this->expense->date->format('Y-m-d');
    $this->title = $this->expense->title;
    $this->description = $this->expense->description;
    $this->currency_id = $this->expense->currency_id;
    $this->amount = $this->expense->amount;
  }

  public function edit()
  {
    $this->modal('expense-edit')->show();
  }

  public function update()
  {
    $this->validate();

    $this->expense->update([
      'number' => $this->number,
      'date' => $this->date,
      'title' => $this->title,
      'description' => $this->description,
      'currency_id' => $this->currency_id,
      'amount' => $this->amount,
    ]);
    
    Flux::toast('Your changes have been saved.');

    $this->modal('expense-edit')->close();
  }
  
  public function remove()
  {
    $this->modal('expense-remove')->show();
  }

  #[Computed]
  public function currencies()
  {
    return Currency::orderBy('label')->get();
  }
};
?>
<flux:row>

  <flux:cell class="w-32">
    {{ $expense->date->format('d.m.Y') }}
  </flux:cell>

  <flux:cell class="w-32">
    {{ $expense->number }}
  </flux:cell>

  <flux:cell>
    {{ $expense->description }}
  </flux:cell>

  {{-- <flux:cell class="w-24">
    {{ $expense->currency->label }}
  </flux:cell> --}}

  <flux:cell class="w-36">
    <div class="flex justify-between items-center">
      <flux:badge inset="top right" size="sm" class="text-zinc-300">
        {{ $expense->currency->label }}
      </flux:badge>
      {{ number_format($expense->amount, 2, '.', '') }}
    </div>
  </flux:cell>

  <flux:cell class="flex justify-end">
    <flux:dropdown align="end">
      <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
      <flux:menu class="min-w-32">
        <flux:menu.item icon="pencil-square" wire:click="edit">Edit</flux:menu.item>
        <flux:menu.item icon="trash" variant="danger" wire:click="remove">Remove</flux:menu.item>
      </flux:menu>
    </flux:dropdown>

    <flux:modal name="expense-remove" class="min-w-[22rem] space-y-6">
      <form class="space-y-6" wire:submit="$parent.remove({{ $expense->id }})">
        <div>
          <flux:heading size="lg">Remove expense?</flux:heading>
          <flux:subheading>
            <p>You're about to delete this expense.</p>
            <p>This action cannot be reversed.</p>
          </flux:subheading>
        </div>
        <div class="flex justify-between gap-2">
          <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
          </flux:modal.close>
          <flux:button type="submit" variant="danger">Delete expense</flux:button>
        </div>
      </form>
    </flux:modal>

    <flux:modal name="expense-edit" variant="flyout">
      <form wire:submit="update" class="space-y-6">
        <div>
          <flux:heading size="lg">Edit Expense</flux:heading>
          <flux:subheading>Edit the expense details.</flux:subheading>
        </div>
        <flux:input label="Number" wire:model="number" />
        <flux:input label="Date" type="date" wire:model="date" />
        <flux:input label="Title" wire:model="title" />
        <flux:textarea label="Description" rows="auto" wire:model="description" />
        <div class="relative space-y-6">
          <flux:select label="Currency" wire:model="currency_id" placeholder="Choose currency...">
            @foreach ($this->currencies as $currency)
              <option value="{{ $currency->id }}" {{ $currency->id === $this->currency_id ? 'selected' : '' }}>{{ $currency->label }}</option>
            @endforeach
          </flux:select>
        </div>
        <flux:input label="Amount" wire:model="amount" />
        <div class="flex">
          <flux:spacer />
          <flux:button type="submit" variant="primary">Save changes</flux:button>
        </div>
      </form>
    </flux:modal>
  </flux:cell>

</flux:row>

