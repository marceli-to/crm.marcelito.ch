<?php
use Livewire\Volt\Component;
use App\Models\Company;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;

new class extends Component {

  #[Rule('string|required')]
  public $name;

  #[Rule('string|required|unique:companies,acronym')]
  public $acronym;

  #[Rule('string|nullable')]
  public $byline;

  #[Rule('string|nullable')]  
  public $street;

  #[Rule('string|nullable')]
  public $zip;

  #[Rule('string|required')]
  public $city;

  public function save()
  {
    $this->validate();
    Company::create([
      'name' => $this->name,
      'acronym' => $this->acronym,
      'byline' => $this->byline,
      'street' => $this->street,
      'zip' => $this->zip,
      'city' => $this->city,
    ]);
    $this->reset('name', 'acronym', 'city', 'zip', 'street', 'byline');
    $this->dispatch('company_created');
    Flux::modal('company-create')->close();
  }
};
?>

<form wire:submit="save" class="space-y-6">
  <div>
    <flux:heading size="lg">Create Company</flux:heading>
    <flux:subheading>Add a new company.</flux:subheading>
  </div>
  <flux:input label="Name" wire:model="name" />
  <flux:input label="Acronym" wire:model="acronym" />
  <flux:input label="Byline" wire:model="byline" />
  <flux:input label="Street" wire:model="street" />
  <flux:input label="Zip" wire:model="zip" />
  <flux:input label="City" wire:model="city" />
  <flux:button type="submit" class="w-full !mt-8" variant="primary">Store Company</flux:button>
</form>
