{{-- REDAS Accordion Component --}}

@props([
    'id',
    'title',
    'icon' => 'fas fa-folder',
    'open' => false,
])

<div class="visa-accordion">

    <div class="visa-accordion-card {{ $open ? 'active' : '' }}">

        <div class="visa-accordion-header">

            <div class="visa-accordion-title">

                <i class="{{ $icon }}"></i>

                <span>{{ $title }}</span>

            </div>

            <i class="fas fa-chevron-down visa-accordion-arrow"></i>

        </div>

        <div
            id="{{ $id }}"
            class="visa-accordion-body">

            {{ $slot }}

        </div>

    </div>

</div>