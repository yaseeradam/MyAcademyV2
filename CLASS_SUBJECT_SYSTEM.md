# Class-Subject Template System

## Overview
Automatically assign subjects to all students in a class with the ability to customize per student.

## How It Works

### 1. Assign Subjects to Class
1. Go to **Classes** page
2. Click on a class card
3. Click the **"Subjects"** stat card (shows count)
4. Select subjects for this class
5. Click **"Save Changes"**

### 2. Automatic Assignment
- All current students in the class get the selected subjects
- New students added to the class automatically inherit these subjects

### 3. Individual Student Overrides
Students can have custom subjects via their profile:
- **Add** subjects not in the class template
- **Remove** subjects from the class template

## Database Structure

### Tables Created
1. **class_subject** - Pivot table linking classes to subjects
   - `class_id` - Foreign key to classes
   - `subject_id` - Foreign key to subjects
   - `is_core` - Boolean (future use for required subjects)

2. **student_subject_overrides** - Individual student customizations
   - `student_id` - Foreign key to students
   - `subject_id` - Foreign key to subjects
   - `action` - Enum ('add', 'remove')

### Model Relationships

**SchoolClass Model:**
```php
$class->defaultSubjects // Get all subjects assigned to this class
```

**Student Model:**
```php
$student->subjectOverrides // Get subject overrides (add/remove)
$student->assigned_subjects // Get final list of subjects (class + overrides)
```

## Logic Flow

**Student's Final Subjects:**
1. Start with class default subjects
2. Remove subjects marked as 'remove' in overrides
3. Add subjects marked as 'add' in overrides
4. Return final list

**When Student Changes Class:**
1. Subject overrides are automatically cleared
2. Student gets new class's default subjects
3. Works for both:
   - Promotion system (bulk promotion)
   - Direct class change (edit student)

## Benefits
- ✅ Faster setup - Configure once per class
- ✅ Consistency - All students get same core subjects
- ✅ Flexibility - Can still customize per student
- ✅ Less errors - No forgetting to assign subjects
- ✅ Bulk operations - Manage entire class at once

## Future Enhancements
- Mark subjects as "Core" (required, cannot be removed)
- Bulk student subject management
- Subject templates (e.g., "Science Stream", "Arts Stream")
- Auto-sync when students change classes
