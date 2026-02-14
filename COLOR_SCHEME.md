# MyAcademy Color Scheme Guide

## Approach 1: Consistent Sky Blue Theme (All Pages)
All pages use the same sky blue gradient for visual consistency.

### Header Gradient
```
from-sky-500 via-blue-500 to-cyan-600
```

### Card Styling
- Border: `border-sky-100`
- Background: `bg-gradient-to-br from-white to-sky-50/30`
- Shadow: `shadow-lg`
- Backdrop: `backdrop-blur-sm`

### Form Elements
- Border: `border-sky-200`
- Focus: `focus:border-sky-500 focus:ring-sky-500`
- Labels: `text-sky-700`

### Buttons
- Primary: `bg-gradient-to-r from-sky-500 to-blue-600`
- Hover: `hover:from-sky-600 hover:to-blue-700`

### Table Headers
- Background: `bg-gradient-to-r from-sky-500 to-blue-600`
- Text: `text-white`

---

## Approach 2: Section-Specific Color Variations

### Dashboard
**Gradient:** `from-slate-700 via-slate-800 to-slate-900`
- Neutral, professional tone for overview page

### Students (Academic)
**Gradient:** `from-blue-500 via-indigo-500 to-purple-600`
- Blue represents learning and knowledge
- Cards: `border-blue-100`, `bg-gradient-to-br from-white to-blue-50/30`

### Teachers (Academic)
**Gradient:** `from-orange-500 via-amber-500 to-yellow-500`
- Warm colors for educators
- Cards: `border-orange-100`, `bg-gradient-to-br from-white to-orange-50/30`

### Classes/Subjects (Academic)
**Gradient:** `from-purple-500 via-violet-500 to-indigo-600`
- Purple for academic structure
- Cards: `border-purple-100`, `bg-gradient-to-br from-white to-purple-50/30`

### Attendance
**Gradient:** `from-teal-500 via-cyan-500 to-blue-500`
- Teal for tracking/monitoring
- Cards: `border-teal-100`, `bg-gradient-to-br from-white to-teal-50/30`

### Results/Scores/Broadsheet
**Gradient:** `from-green-500 via-emerald-500 to-teal-600`
- Green for achievement/success
- Cards: `border-green-100`, `bg-gradient-to-br from-white to-green-50/30`

### Billing/Accounts (Finance)
**Gradient:** `from-emerald-500 via-green-500 to-teal-600`
- Green for money/finance
- Cards: `border-emerald-100`, `bg-gradient-to-br from-white to-emerald-50/30`

### Messages
**Gradient:** `from-pink-500 via-rose-500 to-red-500`
- Pink/rose for communication
- Cards: `border-pink-100`, `bg-gradient-to-br from-white to-pink-50/30`

### Examination
**Gradient:** `from-indigo-500 via-blue-500 to-cyan-600`
- Indigo for formal assessment
- Cards: `border-indigo-100`, `bg-gradient-to-br from-white to-indigo-50/30`

### Timetable (Already Done)
**Gradient:** `from-sky-500 via-blue-500 to-cyan-600`
- Sky blue for scheduling
- Cards: `border-sky-100`, `bg-gradient-to-br from-white to-sky-50/30`

### Settings
**Gradient:** `from-gray-600 via-slate-700 to-gray-800`
- Gray for system/configuration
- Cards: `border-gray-100`, `bg-gradient-to-br from-white to-gray-50/30`

---

## Implementation Pattern

### Header Structure
```blade
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[COLOR1] via-[COLOR2] to-[COLOR3] p-8 shadow-2xl">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,...')] opacity-30"></div>
    <div class="relative flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-white">[PAGE TITLE]</h1>
            <p class="mt-2 text-[COLOR]-100">[SUBTITLE]</p>
        </div>
        <div class="flex gap-3">
            [ACTION BUTTONS]
        </div>
    </div>
</div>
```

### Card Structure
```blade
<div class="rounded-2xl border border-[COLOR]-100 bg-gradient-to-br from-white to-[COLOR]-50/30 p-6 shadow-lg backdrop-blur-sm">
    [CONTENT]
</div>
```

### Icon Badge
```blade
<div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-[COLOR]-500 to-[COLOR]-600 text-white shadow-lg">
    [SVG ICON]
</div>
```

---

## Recommendation

**Approach 2 (Section-Specific Colors)** is recommended because:
1. Visual hierarchy - Users instantly know which section they're in
2. Better UX - Color coding aids navigation and memory
3. Professional - Shows attention to detail and thoughtful design
4. Engaging - More visually interesting than monochrome

**Approach 1 (Consistent Sky Blue)** works if:
1. Brand identity requires single color
2. Simpler maintenance needed
3. Minimalist aesthetic preferred
