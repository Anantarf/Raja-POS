<?php

namespace App\Livewire\Admin;

use App\Models\Brand;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Brands extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editingBrandId = null;

    public $name = '';

    protected $paginationTheme = 'tailwind';

    public function openModal($id = null)
    {
        $this->resetForm();
        if ($id) {
            $b = Brand::findOrFail($id);
            $this->editingBrandId = $b->id;
            $this->name = $b->name;
        }
        $this->showModal = true;
    }

    public function saveBrand()
    {
        abort_unless(auth()->user()->can($this->editingBrandId ? 'product.update' : 'product.create'), 403);
        $this->validate(['name' => 'required|string|max:255']);

        Brand::updateOrCreate(
            ['id' => $this->editingBrandId],
            ['name' => $this->name, 'slug' => Str::slug($this->name)]
        );

        $this->showModal = false;
        $this->dispatch('notify', message: 'Brand berhasil disimpan.', type: 'success');
    }

    public function deleteBrand($id)
    {
        abort_unless(auth()->user()->can('product.delete'), 403);
        Brand::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Brand berhasil dihapus.', type: 'danger');
    }

    private function resetForm()
    {
        $this->editingBrandId = null;
        $this->name = '';
    }

    public function render()
    {
        $brands = Brand::where('name', 'like', '%'.$this->search.'%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.brands', [
            'brands' => $brands,
        ])->layout('components.layouts.admin', ['title' => 'Merk / Brand']);
    }
}
