{{--
Component: Audit Log Button
Usage: @include('components.audit-log-button', ['model' => $model, 'displayName' => $model->nama])
--}}

@php
    $modelClass = get_class($model);
    $modelId = $model->id;
    $displayName = $displayName ?? ($model->nama ?? $model->name ?? 'Item #' . $model->id);
@endphp

<button type="button"
        onclick="showAuditLog('{{ $modelClass }}', '{{ $modelId }}', '{{ $displayName }}')"
        class="text-purple-600 hover:text-purple-800 hover:underline font-medium cursor-pointer"
        title="Lihat Riwayat Perubahan">
    Riwayat
</button>
