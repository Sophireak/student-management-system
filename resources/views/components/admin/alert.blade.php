@if (session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-md text-sm">
        {{ session('error') }}
    </div>
@endif

@if (session('warning'))
    <div class="mb-4 px-4 py-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md text-sm">
        {{ session('warning') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-md text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif