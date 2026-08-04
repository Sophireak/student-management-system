<div class="mt-8 grid grid-cols-2 gap-8 text-sm">
    <div class="text-center">
        <p>បានឃើញនិងឯកភាព</p>
        <p class="font-bold mt-1">នាយកសាលា</p>
        <div class="h-16"></div>
    </div>

    <div class="text-center text-blue-700">
        <p>
            ថ្ងៃ{{ now()->format('l') }} {{ now()->translatedFormat('d F Y') }}
        </p>
        <p>{{ config('app.school_city', 'ក្រុងបាត់ដំបង') }} ថ្ងៃទី{{ now()->format('d') }} ខែ{{ now()->translatedFormat('F') }} ឆ្នាំ{{ now()->format('Y') }}</p>
        <p class="font-bold mt-1">គ្រូទទួលបន្ទុកថ្នាក់</p>
        <div class="h-16"></div>
        <p class="font-semibold">{{ auth()->user()->name }}</p>
    </div>
</div>