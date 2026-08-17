@php
    $labels = ['Personal', 'Address', 'Education', 'Experience', 'Documents', 'Additional', 'Declaration'];
@endphp
<div class="mb-4">
    <div class="d-flex justify-content-between small text-secondary mb-1">
        <span>Step {{ $step }} of {{ $totalSteps }} — {{ $labels[$step - 1] }}</span>
        <span>{{ round(($step / $totalSteps) * 100) }}% Complete</span>
    </div>
    <div class="progress" style="height: 6px;">
        <div class="progress-bar bg-primary" style="width: {{ round(($step / $totalSteps) * 100) }}%;"></div>
    </div>
</div>
