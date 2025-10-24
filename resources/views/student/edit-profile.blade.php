@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div style="min-height: calc(100vh - 64px); background: #100f21; padding: 2rem;">
    <div style="width: 100%; margin: 0 auto;">
        
        @if($errors->any())
            <div style="background: #2d1a1f; color: #F9896B; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #F9896B;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Update Profile Form -->
        <div style="background: #1c1a36; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);">
            <h2 style="color: #F1F5FB; font-size: 1.5rem; margin: 0 0 1.5rem 0; border-bottom: 2px solid #302e4a; padding-bottom: 0.75rem;">
                Update Profile
            </h2>

            <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Profile Image Upload -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #C1CEE5; font-size: 0.9rem; margin-bottom: 0.5rem;">
                        Profile Image
                    </label>
                    <input type="file" 
                           name="profile_image" 
                           accept="image/*"
                           style="width: 100%; padding: 0.75rem; background: #302e4a; border: 1px solid #401a75; border-radius: 8px; color: #F1F5FB; font-size: 1rem;">
                    <small style="color: #C1CEE5; font-size: 0.85rem; display: block; margin-top: 0.25rem;">
                        Accepted formats: JPG, PNG, GIF (Max: 2MB)
                    </small>
                </div>

                <!-- Name -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #C1CEE5; font-size: 0.9rem; margin-bottom: 0.5rem;">
                        Full Name
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $student->name) }}"
                           required
                           style="width: 100%; padding: 0.75rem; background: #302e4a; border: 1px solid #401a75; border-radius: 8px; color: #F1F5FB; font-size: 1rem;">
                </div>

                <!-- Department -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #C1CEE5; font-size: 0.9rem; margin-bottom: 0.5rem;">
                        Department
                    </label>
                    <input type="text" 
                           name="department" 
                           value="{{ old('department', $student->department) }}"
                           required
                           style="width: 100%; padding: 0.75rem; background: #302e4a; border: 1px solid #401a75; border-radius: 8px; color: #F1F5FB; font-size: 1rem;">
                </div>

                <!-- Year and Semester -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; color: #C1CEE5; font-size: 0.9rem; margin-bottom: 0.5rem;">
                            Year
                        </label>
                        <input type="number" 
                               name="year" 
                               value="{{ old('year', $student->year) }}"
                               min="1" 
                               max="5"
                               required
                               style="width: 100%; padding: 0.75rem; background: #302e4a; border: 1px solid #401a75; border-radius: 8px; color: #F1F5FB; font-size: 1rem;">
                    </div>
                    <div>
                        <label style="display: block; color: #C1CEE5; font-size: 0.9rem; margin-bottom: 0.5rem;">
                            Semester
                        </label>
                        <input type="text" 
                               name="semester" 
                               value="{{ old('semester', $student->semester) }}"
                               required
                               style="width: 100%; padding: 0.75rem; background: #302e4a; border: 1px solid #401a75; border-radius: 8px; color: #F1F5FB; font-size: 1rem;">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" 
                            style="flex: 1; padding: 0.875rem; background: #401a75; color: #F1F5FB; border: none; border-radius: 8px; font-size: 1rem; font-weight: 500; cursor: pointer; transition: background 0.3s;">
                        Save Changes
                    </button>
                    
                    <a href="{{ route('student.profile') }}" 
                       style="flex: 1; padding: 0.875rem; background: #302e4a; color: #F1F5FB; border: none; border-radius: 8px; font-size: 1rem; font-weight: 500; cursor: pointer; text-decoration: none; text-align: center; display: block; transition: background 0.3s;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

<style>
    button:hover, a:hover {
        opacity: 0.9;
    }
</style>
@endsection
