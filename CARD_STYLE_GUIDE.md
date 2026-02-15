# MyAcademy Card Style Guide

## Primary Card Style (CBT Question Card)

### Base Card Container
```html
<div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-slate-100">
  <!-- Content here -->
</div>
```

### Card with Hover Effect
```html
<div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-slate-100 hover:shadow-lg transition-all">
  <!-- Content here -->
</div>
```

---

## Option/Choice Cards (Interactive)

### Unselected State
```html
<label class="group flex cursor-pointer items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3.5 transition-all hover:bg-white hover:shadow-sm">
  <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-white text-xs font-bold text-slate-700 group-hover:text-amber-700">
    A
  </div>
  <span class="min-w-0 flex-1 text-sm font-medium leading-relaxed text-slate-800">
    Option text here
  </span>
</label>
```

### Selected State
```html
<label class="group flex cursor-pointer items-start gap-3 rounded-xl border border-slate-100 bg-amber-500 p-3.5 shadow-md transition-all">
  <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-white text-xs font-bold text-amber-700">
    A
  </div>
  <span class="min-w-0 flex-1 text-sm font-semibold leading-relaxed text-white">
    Option text here
  </span>
</label>
```

---

## Color Palette

### Primary Colors
- **Primary:** `amber-500` (#F59E0B)
- **Primary Hover:** `amber-600` (#D97706)
- **Primary Light:** `amber-50` (#FFFBEB)
- **Primary Text:** `amber-700` (#B45309)

### Neutral Colors
- **Background:** `white` (#FFFFFF)
- **Surface:** `slate-50` (#F8FAFC)
- **Border:** `slate-100` (#F1F5F9)
- **Text Primary:** `slate-900` (#0F172A)
- **Text Secondary:** `slate-700` (#334155)
- **Text Muted:** `slate-600` (#475569)

### Accent Colors
- **Success:** `emerald-600` (#059669)
- **Error:** `rose-600` (#E11D48)
- **Info:** `blue-600` (#2563EB)

---

## Spacing & Sizing

### Border Radius
- Small: `rounded-lg` (8px)
- Medium: `rounded-xl` (12px)
- Large: `rounded-2xl` (16px)
- Full: `rounded-full` (9999px)

### Padding
- Small: `p-3` (12px)
- Medium: `p-4` (16px)
- Large: `p-5` (20px)
- Extra Large: `p-6` (24px)

### Shadows
- Small: `shadow-sm`
- Medium: `shadow-md`
- Large: `shadow-lg`
- Extra Large: `shadow-xl`

### Ring (Borders)
- Thin: `ring-1`
- Medium: `ring-2`
- Color: `ring-slate-100` or `ring-slate-200`

---

## Typography

### Font Weights
- Regular: `font-medium` (500)
- Semibold: `font-semibold` (600)
- Bold: `font-bold` (700)

### Font Sizes
- Extra Small: `text-xs` (12px)
- Small: `text-sm` (14px)
- Base: `text-base` (16px)
- Large: `text-lg` (18px)
- Extra Large: `text-xl` (20px)

---

## Common Components

### Badge
```html
<span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
  Badge Text
</span>
```

### Button Primary
```html
<button class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-amber-600 transition-all">
  Button Text
</button>
```

### Button Secondary
```html
<button class="rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-200 transition-all">
  Button Text
</button>
```

### Input Field
```html
<input class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-amber-400 focus:ring-amber-300" />
```

### Textarea
```html
<textarea class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-amber-400 focus:ring-amber-300" rows="6"></textarea>
```

---

## Usage Examples

### Info Card
```html
<div class="rounded-2xl bg-slate-50 p-6 shadow-sm ring-1 ring-slate-100">
  <h3 class="text-lg font-bold text-slate-900">Card Title</h3>
  <p class="mt-2 text-sm text-slate-600">Card description text here.</p>
</div>
```

### Success Card
```html
<div class="rounded-2xl bg-emerald-50 p-6 shadow-sm ring-1 ring-emerald-100">
  <h3 class="text-lg font-bold text-emerald-900">Success!</h3>
  <p class="mt-2 text-sm text-emerald-700">Operation completed successfully.</p>
</div>
```

### Warning Card
```html
<div class="rounded-2xl bg-amber-50 p-6 shadow-sm ring-1 ring-amber-100">
  <h3 class="text-lg font-bold text-amber-900">Warning</h3>
  <p class="mt-2 text-sm text-amber-700">Please review this information.</p>
</div>
```

---

## Transitions

Always add smooth transitions:
```html
class="transition-all duration-200"
```

For hover effects:
```html
class="hover:shadow-lg hover:scale-[1.02] transition-all duration-200"
```

---

## Notes

- **Primary color is Amber** (`amber-500`) - use for selected states, CTAs, and highlights
- Always use `rounded-2xl` for main cards
- Use `rounded-xl` for nested elements (options, buttons)
- Combine `shadow-md` with `ring-1 ring-slate-100` for depth
- Add `transition-all` for smooth interactions
- Use `group` and `group-hover:` for parent-child hover effects
