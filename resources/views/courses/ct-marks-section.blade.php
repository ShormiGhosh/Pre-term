<!-- CT Marks Section -->
<div class="placeholder-content" id="section-ct-marks">
    <div class="content-header">
        <h1 class="content-title">CT Marks</h1>
        <p class="content-subtitle">{{ $course->course_code }} - {{ $course->course_title }}</p>
    </div>

    @php
        $allCTs = $course->ctSchedules()->orderBy('ct_datetime', 'asc')->get();
    @endphp

    <div class="content-section">
        @if($allCTs->count() > 0)
            @if($user instanceof \App\Models\Teacher && $user->id === $course->teacher_id)
                <!-- Teacher View -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 class="section-title">All Students CT Marks</h2>
                    <div style="display: flex; gap: 1rem;">
                        <button type="button" id="editMarksBtn" class="btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); padding: 0.75rem 1.5rem; border-radius: 8px; border: none; color: white; font-weight: 600; cursor: pointer;">
                            <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 1.2rem;">edit</span>
                            Edit Marks
                        </button>
                        <a href="{{ route('ct-marks.download', $course->id) }}" class="btn-primary" style="background: linear-gradient(135deg, #3b82f6, #2563eb); padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; color: white; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                            <span class="material-symbols-outlined" style="font-size: 1.2rem;">download</span>
                            Download Result
                        </a>
                    </div>
                </div>

                <form id="ctMarksForm" action="{{ route('ct-marks.save', $course->id) }}" method="POST">
                    @csrf
                    <div style="overflow-x: auto; background: rgba(255,255,255,0.03); border-radius: 12px; padding: 1rem;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                            <thead>
                                <tr style="background: linear-gradient(135deg, rgba(64, 26, 117, 0.3), rgba(94, 42, 158, 0.3)); border-bottom: 2px solid rgba(255,255,255,0.1);">
                                    <th style="padding: 1rem; text-align: left; color: #F1F5FB; font-weight: 600;">Roll</th>
                                    <th style="padding: 1rem; text-align: left; color: #F1F5FB; font-weight: 600;">Student Name</th>
                                    @foreach($allCTs as $ct)
                                        <th style="padding: 1rem; text-align: center; color: #F1F5FB; font-weight: 600;">
                                            {{ $ct->ct_name }}<br>
                                            <span style="font-size: 0.75rem; color: #8894AC;">({{ $ct->total_marks }})</span>
                                        </th>
                                    @endforeach
                                    @foreach($allCTs as $ct)
                                        <th style="padding: 1rem; text-align: center; color: #10b981; font-weight: 600; font-size: 0.85rem;">
                                            {{ $ct->ct_name }}<br>Avg
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students()->orderBy('roll_number', 'asc')->get() as $student)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td style="padding: 1rem; color: #F1F5FB;">{{ $student->roll_number }}</td>
                                        <td style="padding: 1rem; color: #F1F5FB;">{{ $student->name }}</td>
                                        @foreach($allCTs as $ct)
                                            @php
                                                $mark = \App\Models\CTMark::where('student_id', $student->id)
                                                                         ->where('ct_schedule_id', $ct->id)
                                                                         ->first();
                                            @endphp
                                            <td style="padding: 0.5rem; text-align: center;">
                                                <input type="hidden" name="marks[{{ $loop->parent->index * $allCTs->count() + $loop->index }}][student_id]" value="{{ $student->id }}">
                                                <input type="hidden" name="marks[{{ $loop->parent->index * $allCTs->count() + $loop->index }}][ct_schedule_id]" value="{{ $ct->id }}">
                                                <input 
                                                    type="number" 
                                                    name="marks[{{ $loop->parent->index * $allCTs->count() + $loop->index }}][marks_obtained]" 
                                                    value="{{ $mark ? $mark->marks_obtained : '' }}"
                                                    step="0.01"
                                                    min="0"
                                                    max="{{ $ct->total_marks }}"
                                                    placeholder="-"
                                                    class="marks-input"
                                                    disabled
                                                    style="width: 70px; padding: 0.5rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: #F1F5FB; text-align: center;"
                                                >
                                            </td>
                                        @endforeach
                                        @foreach($allCTs as $ct)
                                            @php
                                                $avg = \App\Models\CTMark::where('ct_schedule_id', $ct->id)
                                                                        ->whereNotNull('marks_obtained')
                                                                        ->avg('marks_obtained');
                                            @endphp
                                            <td style="padding: 1rem; text-align: center; color: #10b981; font-weight: 600;">
                                                {{ $avg ? number_format($avg, 2) : '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 1.5rem; display: none;" id="saveButtonContainer">
                        <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #10b981, #059669); padding: 0.75rem 2rem; border-radius: 8px; border: none; color: white; font-weight: 600; cursor: pointer; font-size: 1rem;">
                            <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 1.2rem;">save</span>
                            Save Marks
                        </button>
                        <button type="button" id="cancelEditBtn" class="btn-secondary" style="background: rgba(239, 68, 68, 0.2); padding: 0.75rem 2rem; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; font-weight: 600; cursor: pointer; font-size: 1rem; margin-left: 1rem;">
                            Cancel
                        </button>
                    </div>
                </form>

            @else
                <!-- Student View -->
                <div style="margin-bottom: 1.5rem;">
                    <h2 class="section-title">My CT Marks</h2>
                </div>

                <div style="overflow-x: auto; background: rgba(255,255,255,0.03); border-radius: 12px; padding: 1rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, rgba(64, 26, 117, 0.3), rgba(94, 42, 158, 0.3)); border-bottom: 2px solid rgba(255,255,255,0.1);">
                                <th style="padding: 1rem; text-align: left; color: #F1F5FB; font-weight: 600;">CT Name</th>
                                <th style="padding: 1rem; text-align: center; color: #F1F5FB; font-weight: 600;">Total Marks</th>
                                <th style="padding: 1rem; text-align: center; color: #F1F5FB; font-weight: 600;">Marks Obtained</th>
                                <th style="padding: 1rem; text-align: center; color: #10b981; font-weight: 600;">Class Average</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allCTs as $ct)
                                @php
                                    $mark = \App\Models\CTMark::where('student_id', $user->id)
                                                             ->where('ct_schedule_id', $ct->id)
                                                             ->first();
                                    $avg = \App\Models\CTMark::where('ct_schedule_id', $ct->id)
                                                            ->whereNotNull('marks_obtained')
                                                            ->avg('marks_obtained');
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td style="padding: 1rem; color: #F1F5FB;">{{ $ct->ct_name }}</td>
                                    <td style="padding: 1rem; text-align: center; color: #8894AC;">{{ $ct->total_marks }}</td>
                                    <td style="padding: 1rem; text-align: center; color: {{ $mark ? '#10b981' : '#8894AC' }}; font-weight: {{ $mark ? '600' : 'normal' }};">
                                        {{ $mark ? $mark->marks_obtained : '-' }}
                                    </td>
                                    <td style="padding: 1rem; text-align: center; color: #10b981; font-weight: 600;">
                                        {{ $avg ? number_format($avg, 2) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📊</div>
                <p class="empty-state-text">No CT schedules available yet</p>
                <p style="color: #8894AC; font-size: 0.9rem; margin-top: 0.5rem;">CT marks will appear here once CTs are scheduled</p>
            </div>
        @endif
    </div>
</div>

<script>
// CT Marks Edit Functionality
const editMarksBtn = document.getElementById('editMarksBtn');
const cancelEditBtn = document.getElementById('cancelEditBtn');
const saveButtonContainer = document.getElementById('saveButtonContainer');
const marksInputs = document.querySelectorAll('.marks-input');

if (editMarksBtn) {
    editMarksBtn.addEventListener('click', function() {
        // Enable all inputs
        marksInputs.forEach(input => input.disabled = false);
        
        // Show save/cancel buttons
        saveButtonContainer.style.display = 'block';
        
        // Hide edit button
        editMarksBtn.style.display = 'none';
        
        // Focus first input
        if (marksInputs.length > 0) {
            marksInputs[0].focus();
        }
    });
}

if (cancelEditBtn) {
    cancelEditBtn.addEventListener('click', function() {
        // Disable all inputs
        marksInputs.forEach(input => input.disabled = true);
        
        // Hide save/cancel buttons
        saveButtonContainer.style.display = 'none';
        
        // Show edit button
        editMarksBtn.style.display = 'inline-flex';
        
        // Reload page to reset values
        location.reload();
    });
}
</script>
