<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use App\Models\Company;

new class extends Component {

  public Company $company;

  #[Rule('string|required')]
  public $name;

  #[Rule('string|required')]
  public $acronym;

  #[Rule('string|nullable')]
  public $byline;

  #[Rule('string|nullable')]
  public $street;

  #[Rule('string|nullable')]
  public $zip;

  #[Rule('string|required')]
  public $city;

  public function mount()
  {
    $this->name = $this->company->name;
    $this->acronym = $this->company->acronym;
    $this->byline = $this->company->byline;
    $this->street = $this->company->street;
    $this->zip = $this->company->zip;
    $this->city = $this->company->city;
  }

  public function edit()
  {
    $this->modal('company-edit')->show();
  }

  public function update()
  {
    $this->validate();

    $this->company->update([
      'name' => $this->name,
      'acronym' => $this->acronym,
      'byline' => $this->byline,
      'street' => $this->street,
      'zip' => $this->zip,
      'city' => $this->city,
    ]);
    
    Flux::toast('Your changes have been saved.');

    $this->modal('company-edit')->close();
  }
  
  public function remove()
  {
    $this->modal('company-remove')->show();
  }
};
?>
<flux:row>

  <flux:cell variant="strong" class="w-1/2">
    {{ $company->name }}
  </flux:cell>

  <flux:cell class="w-48">
    <flux:badge inset="top right" size="sm" class="text-zinc-300">
      {{ $company->acronym }}
    </flux:badge>
  </flux:cell>

  <flux:cell>
    {{ $company->city }}
  </flux:cell>

  <flux:cell class="flex justify-end">
    <flux:dropdown align="end">
      <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
      <flux:menu class="min-w-32">
        <flux:menu.item icon="pencil-square" wire:click="edit">Edit</flux:menu.item>
        <flux:menu.item icon="trash" variant="danger" wire:click="remove">Remove</flux:menu.item>
      </flux:menu>
    </flux:dropdown>

    <flux:modal name="company-remove" class="min-w-[22rem] space-y-6">
      <form class="space-y-6" wire:submit="$parent.remove({{ $company->id }})">
        <div>
          <flux:heading size="lg">Remove company?</flux:heading>
          <flux:subheading>
            <p>You're about to delete this company.</p>
            <p>This action cannot be reversed.</p>
          </flux:subheading>
        </div>
        <div class="flex justify-between gap-2">
          <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
          </flux:modal.close>
          <flux:button type="submit" variant="danger">Delete company</flux:button>
        </div>
      </form>
    </flux:modal>

    <flux:modal name="company-edit" variant="flyout">
      <form wire:submit="update" class="space-y-6">
        <div>
          <flux:heading size="lg">Edit Company</flux:heading>
          <flux:subheading>Edit the company details.</flux:subheading>
        </div>
        <flux:input label="Name" wire:model="name" />
        <flux:input label="Acronym" wire:model="acronym" />
        <flux:input label="Byline" wire:model="byline" />
        <flux:input label="Street" wire:model="street" />
        <flux:input label="Zip" wire:model="zip" />
        <flux:input label="City" wire:model="city" />
        <flux:button type="submit" class="w-full !mt-8" variant="primary">Update Company</flux:button>
      </form>
    </flux:modal>
  </flux:cell>
</flux:row>

