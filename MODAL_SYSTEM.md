# Global Modal System

## ✅ Setup Complete!

The global modal system is now active across all pages.

## Usage in Livewire Components

### 1. Add the Trait

```php
use App\Traits\DispatchesModals;

class YourComponent extends Component
{
    use DispatchesModals;
    
    public function save()
    {
        // Your save logic...
        
        $this->dispatchSuccessModal(
            'Success!',
            'Record saved successfully.'
        );
    }
    
    public function delete()
    {
        // Your delete logic...
        
        $this->dispatchErrorModal(
            'Deleted',
            'Record has been deleted.'
        );
    }
}
```

### 2. Available Methods

```php
// Success (green checkmark)
$this->dispatchSuccessModal('Title', 'Message');

// Error (red X)
$this->dispatchErrorModal('Title', 'Message');

// Warning (yellow triangle)
$this->dispatchWarningModal('Title', 'Message');

// Info (blue info icon)
$this->dispatchInfoModal('Title', 'Message');
```

## Examples

### Student Added
```php
$this->dispatchSuccessModal(
    'Student Added',
    "Student {$student->full_name} has been added successfully."
);
```

### Class Deleted
```php
$this->dispatchErrorModal(
    'Class Deleted',
    "Class {$class->name} has been permanently deleted."
);
```

### Backup Warning
```php
$this->dispatchWarningModal(
    'Backup Required',
    'No backup found for the last 7 days. Please create a backup.'
);
```

## Already Implemented

✅ Student Form (Add/Edit)
✅ Backup page (already has modals)

## TODO: Add to These Components

- [ ] Classes (add/edit/delete)
- [ ] Teachers (add/edit/delete)
- [ ] Subjects (add/edit/delete)
- [ ] Results (score entry save)
- [ ] Billing (payment recorded)
- [ ] Attendance (marked)
- [ ] Users (add/edit/delete)
- [ ] Settings (saved)

## Quick Implementation

For any Livewire component:

1. Add `use App\Traits\DispatchesModals;`
2. Replace success redirects with modal + optional redirect
3. Replace error messages with error modals

Example:
```php
// Before
session()->flash('success', 'Saved!');
return redirect()->route('index');

// After
$this->dispatchSuccessModal('Saved!', 'Your changes have been saved.');
return $this->redirect(route('index'), navigate: true);
```
