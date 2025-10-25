<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CT Marks - {{ $course->course_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 20px;
            background: white;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #401a75;
        }
        
        .header h1 {
            color: #401a75;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header h2 {
            color: #666;
            font-size: 20px;
            font-weight: normal;
        }
        
        .course-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .course-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .course-info strong {
            color: #401a75;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        thead {
            background: linear-gradient(135deg, #401a75, #5e2a9e);
            color: white;
        }
        
        th {
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 12px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        th.roll-col {
            text-align: left;
            width: 80px;
        }
        
        th.name-col {
            text-align: left;
            width: 200px;
        }
        
        tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }
        
        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        tbody tr:hover {
            background: #f0f0f0;
        }
        
        td {
            padding: 10px 8px;
            text-align: center;
            font-size: 13px;
            border: 1px solid #e0e0e0;
        }
        
        td.roll-col {
            text-align: left;
            font-weight: 600;
            color: #401a75;
        }
        
        td.name-col {
            text-align: left;
        }
        
        .class-average-row {
            background: #e8f5e9 !important;
            font-weight: 600;
        }
        
        .class-average-row td {
            color: #2e7d32;
            border-top: 2px solid #4caf50;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .print-date {
            margin-top: 10px;
            font-style: italic;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CT Marks Report</h1>
        <h2>{{ $course->course_code }}: {{ $course->course_title }}</h2>
    </div>

    <div class="course-info">
        <p><strong>Department:</strong> {{ $course->department ?? 'N/A' }}</p>
        <p><strong>Course Credit:</strong> {{ $course->course_credit }}</p>
        <p><strong>Instructor:</strong> {{ $course->teacher->name }}</p>
        <p><strong>Total Students:</strong> {{ $students->count() }}</p>
        <p><strong>Total CTs:</strong> {{ $ctSchedules->count() }}</p>
    </div>

    @if($ctSchedules->count() > 0)
        <table>
            <thead>
                <tr>
                    <th class="roll-col">Roll</th>
                    <th class="name-col">Student Name</th>
                    @foreach($ctSchedules as $ct)
                        <th>
                            {{ $ct->ct_name }}<br>
                            <span style="font-size: 10px; font-weight: normal;">({{ $ct->total_marks }})</span>
                        </th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marksData as $studentData)
                    <tr>
                        <td class="roll-col">{{ $studentData['roll'] }}</td>
                        <td class="name-col">{{ $studentData['name'] }}</td>
                        @php
                            $totalMarks = 0;
                            $totalPossible = 0;
                            $ctCount = 0;
                        @endphp
                        @foreach($ctSchedules as $ct)
                            @php
                                $mark = $studentData['marks'][$ct->id] ?? '-';
                                if ($mark !== '-') {
                                    $totalMarks += $mark;
                                    $totalPossible += $ct->total_marks;
                                    $ctCount++;
                                }
                            @endphp
                            <td>{{ $mark }}</td>
                        @endforeach
                        <td style="font-weight: 600;">{{ $totalMarks > 0 ? $totalMarks : '-' }}</td>
                    </tr>
                @endforeach
                
                <!-- Class Average Row -->
                <tr class="class-average-row">
                    <td colspan="2" style="text-align: right; padding-right: 15px;">Class Average:</td>
                    @foreach($ctSchedules as $ct)
                        <td>{{ $classAverages[$ct->id] }}</td>
                    @endforeach
                    <td>-</td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px;">
            <p style="font-size: 16px; color: #666;">No CT schedules available for this course.</p>
        </div>
    @endif

    <div class="footer">
        <p><strong>Pre-term Attendance System</strong></p>
        <p class="print-date">Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
