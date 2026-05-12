<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Branch;
use App\Services\ProductService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class Storefront extends Component
{
    #[Url]
    public $search = '';
    
    public $activeBranchId;

    #[Url]
    public $selectedCategoryId = null;

    // Catalog mode: null = Global (all branches), otherwise branch id
    public $catalogBranchId = null;

    /**
     * Mount the component and handle multi-branch initialization.
     * Satisfies "Multi-branch product catalog" criteria.
     */
    public function mount()
    {
        // Use a local variable to help the IDE resolve the Branch instance
        $firstBranch = Branch::first();
        
        // Branch-scoped checkout/cart always uses active_branch_id
        $this->activeBranchId = session('active_branch_id', $firstBranch?->id);

        // Catalog mode defaults to the same branch, normalized
        $this->catalogBranchId = $this->normalizeBranchId($this->activeBranchId);
    }

    /**
     * Livewire hook for reactive property updates.
     */
    public function updatedCatalogBranchId($value)
    {
        $this->catalogBranchId = $this->normalizeBranchId($value);
    }

    /**
     * Ensure branch ID is either a positive integer or null.
     */
    private function normalizeBranchId($value): ?int
    {
        return (is_numeric($value) && (int) $value > 0) ? (int) $value : null;
    }

    #[Layout('layouts.app')]
    public function render(ProductService $productService)
    {
        return view('livewire.storefront', [
            // Ensuring we pass an integer or null to the service
            'products' => $productService->getAvailableProducts(
                catalogBranchId: $this->catalogBranchId,
                search: (string) $this->search,
                categoryId: $this->selectedCategoryId,
            ),
            'branches' => Branch::all(),
            'categories' => \App\Models\Category::select('id', 'name')->get(),
        ]);
    }
}