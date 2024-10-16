<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Invoice;

new class extends Component {

  public function mount()
  {
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

<div class="max-w-3xl">
  <flux:heading size="xl" class="!mb-6">Revenue</flux:heading>
  <div class="mt-6 grid grid-cols-12 gap-6"> 
  
    <flux:card class="col-span-9 flex justify-between items-center space-x-6">
      <div class="w-full flex justify-between items-center">
        <div class="w-full">
          <flux:heading size="lg" class="!mb-0">
            {!! number_format($this->sumOpenInvoices, 2, '.', '&thinsp;') !!}
          </flux:heading>
          <flux:subheading class="!text-zinc-400">
            Open
          </flux:subheading>
        </div>
        <div>
          <flux:icon.currency-dollar class="text-zinc-300 size-6" />
        </div>
      </div>
      <flux:separator vertical />
      <div class="w-full flex justify-between items-center">
        <div>
          <flux:heading size="lg" class="!mb-0">
            {!! number_format($this->sumPendingInvoices, 2, '.', '&thinsp;') !!}
          </flux:heading>
          <flux:subheading class="!text-zinc-400">
            Pending
          </flux:subheading>
        </div>
        <div>
          <flux:icon.currency-dollar class="text-blue-400 size-6" />
        </div>
      </div>
      <flux:separator vertical />
      <div class="w-full flex justify-between items-center">
        <div>
          <flux:heading size="lg" class="!mb-0">
            {!! number_format($this->sumPaidInvoices, 2, '.', '&thinsp;') !!}
          </flux:heading>
          <flux:subheading class="!text-zinc-400">
            Paid
          </flux:subheading>
        </div>
        <div>
          <flux:icon.currency-dollar class="text-green-400 size-6" />
        </div>
      </div>
    </flux:card>

    <flux:card class="!bg-green-50 !border-green-400/50 col-span-9 flex justify-between items-center">
      <div>
        <flux:heading size="xl" class="!mb-0 font-bold !text-green-600">
          {!! number_format($this->sumInvoices, 2, '.', '&thinsp;') !!}
        </flux:heading>
        <flux:subheading size="lg" class="!text-green-600">
          Expected revenue
        </flux:subheading>
      </div>
      <div>
        <flux:icon.arrow-trending-up class="text-green-400 size-8" />
      </div>
    </flux:card>
  </div>

</div>

