<?php

namespace App\View\Components\colors;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Table extends Component
{
    public mixed $colors;
    public bool $showView;
    public bool $showEdit;
    public bool $showDelete;

    /**
     * Create a new component instance.
     */
    public function __construct(
        mixed $colors,
        bool $showView = true,
        bool $showEdit = true,
        bool $showDelete = true
    ) {
        $this->colors = $colors;
        $this->showView = $showView;
        $this->showEdit = $showEdit;
        $this->showDelete = $showDelete;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.colors.table');
    }
}
