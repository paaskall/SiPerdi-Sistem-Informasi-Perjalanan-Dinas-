<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class AuditTrailIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = Activity::with(['causer', 'subject'])
            ->where(function($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                      ->orWhere('subject_type', 'like', '%' . $this->search . '%')
                      ->orWhereHas('causer', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.audit-trail-index', compact('logs'))->layout('layouts.app');
    }
}