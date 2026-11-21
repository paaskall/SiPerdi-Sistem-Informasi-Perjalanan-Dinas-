@props(['value' => 0, 'prefix' => true])

{{ \App\Support\Rupiah::format((float) $value, $prefix) }}