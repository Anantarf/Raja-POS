<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingCategoryId = null;

    public $name = '';

    protected $paginationTheme = 'tailwind';

    public function openModal($id = null)
    {
        $this->resetForm();
        if ($id) {
            $cat = Category::findOrFail($id);
            $this->editingCategoryId = $cat->id;
            $this->name = $cat->name;
        }
        $this->showModal = true;
    }

    public function saveCategory()
    {
        $this->validate(['name' => 'required|string|max:255']);

        Category::updateOrCreate(
            ['id' => $this->editingCategoryId],
            ['name' => $this->name, 'slug' => Str::slug($this->name)]
        );

        $this->showModal = false;
        $this->dispatch('notify', message: 'Kategori berhasil disimpan.', type: 'success');
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Kategori berhasil dihapus.', type: 'danger');
    }

    private function resetForm()
    {
        $this->editingCategoryId = null;
        $this->name = '';
    }

    public function render()
    {
        $categories = Category::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.categories', [
            'categories' => $categories,
        ])->layout('components.layouts.admin', ['title' => 'Kategori Produk - Raja POS']);
    }
}
