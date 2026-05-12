@props(['title' => null])

@if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800 border border-red-200" role="alert">
        @if ($title)
            <p class="font-medium">{{ $title }}</p>
        @endif
        <ul class="mt-2 list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
