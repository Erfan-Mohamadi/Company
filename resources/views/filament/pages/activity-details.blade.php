<div class="space-y-4 p-6">
    <h2 class="text-xl font-bold">{{ $record->description ?: 'Activity Details' }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <strong>Performed by:</strong> {{ $record->causer?->name ?? 'System' }}
        </div>
        <div>
            <strong>Time:</strong> {{ $record->created_at->format('Y-m-d H:i:s') }}
        </div>
    </div>

    @if ($record->properties)
        <div class="mt-6">
            <strong>Changes:</strong>
            <pre class="bg-gray-100 p-4 rounded mt-2 overflow-auto text-sm">
{{ json_encode($record->properties, JSON_PRETTY_PRINT) }}
            </pre>
        </div>
    @else
        <p class="text-gray-500">No detailed properties recorded.</p>
    @endif

    <div class="mt-4 text-sm text-gray-500">
        Event: <code>{{ $record->event }}</code><br>
        Subject: <code>{{ $record->subject_type }} #{{ $record->subject_id }}</code>
    </div>
</div>
