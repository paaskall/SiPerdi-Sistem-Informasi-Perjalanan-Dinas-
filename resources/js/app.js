import './bootstrap';

import Alpine from 'alpinejs';
import { Livewire, Alpine as LivewireAlpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

window.Alpine = Alpine;
window.Livewire = Livewire;

// Init Alpine
Alpine.start();

// Init Livewire (Wajib buat Livewire v3)
Livewire.start();