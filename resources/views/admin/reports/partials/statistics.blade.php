<div class="mt-4 text-xs space-y-1">
    <p>
        <span class="font-semibold">បញ្ជីបញ្ចូលក្នុងខែនេះចំនួន</span>
        {{ $statistics['total'] }} នាក់ 
        ក្នុងនោះស្រី {{ $statistics['females'] }} នាក់ 
        ប្រុស {{ $statistics['males'] }} នាក់
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
        <p class="text-green-700">
            និទ្ទេស <span class="font-bold">ល្អ</span> មាន 
            {{ $statistics['grade_counts']['very_good'] + $statistics['grade_counts']['excellent'] }} នាក់/
            ស្រី {{ $statistics['grade_counts']['very_good_female'] + $statistics['grade_counts']['excellent_female'] }} នាក់ 
            ត្រូវនឹង 
            {{ $statistics['total_scored'] > 0 
                ? number_format((($statistics['grade_counts']['very_good'] + $statistics['grade_counts']['excellent']) / $statistics['total_scored']) * 100, 2) 
                : '0.00' }}%
        </p>

        <p class="text-pink-600">
            និទ្ទេស <span class="font-bold">ល្អបង្គួរ</span> មាន 
            {{ $statistics['grade_counts']['good'] }} នាក់/
            ស្រី {{ $statistics['grade_counts']['good_female'] }} នាក់ 
            ត្រូវនឹង 
            {{ $statistics['total_scored'] > 0 
                ? number_format(($statistics['grade_counts']['good'] / $statistics['total_scored']) * 100, 2) 
                : '0.00' }}%
        </p>

        <p class="text-green-700">
            និទ្ទេស <span class="font-bold">មធ្យម</span> មាន 
            {{ $statistics['grade_counts']['average'] }} នាក់/
            ស្រី {{ $statistics['grade_counts']['average_female'] }} នាក់ 
            ត្រូវនឹង 
            {{ $statistics['total_scored'] > 0 
                ? number_format(($statistics['grade_counts']['average'] / $statistics['total_scored']) * 100, 2) 
                : '0.00' }}%
        </p>

        <p class="text-red-600">
            និទ្ទេស <span class="font-bold">ខ្សោយ</span> មាន 
            {{ $statistics['grade_counts']['weak'] + $statistics['grade_counts']['fail'] }} នាក់/
            ស្រី {{ $statistics['grade_counts']['weak_female'] + $statistics['grade_counts']['fail_female'] }} នាក់ 
            ត្រូវនឹង 
            {{ $statistics['total_scored'] > 0 
                ? number_format((($statistics['grade_counts']['weak'] + $statistics['grade_counts']['fail']) / $statistics['total_scored']) * 100, 2) 
                : '0.00' }}%
        </p>
    </div>

    <div class="pt-2 space-y-1">
        <p class="text-green-700 font-semibold">
            សិស្សជាប់មធ្យមភាគចំនួន 
            {{ $statistics['pass_count'] }} នាក់ 
            ក្នុងនោះសិស្សស្រី {{ $statistics['pass_female'] }} នាក់ត្រូវនឹង 
            {{ $statistics['pass_percent'] }}%
        </p>
        <p class="text-red-600 font-semibold">
            សិស្សធ្លាក់មធ្យមភាគចំនួន 
            {{ $statistics['fail_count'] }} នាក់ 
            ក្នុងនោះសិស្សស្រី {{ $statistics['fail_female'] }} នាក់ត្រូវនឹង 
            {{ $statistics['fail_percent'] }}%
        </p>
    </div>
</div>