# Certificate Generation Setup

## Installation

Run this command in Laragon Terminal:

```bash
cd C:\laragon\www\myacademy-laravel
php composer.phar update
```

This installs **Intervention Image** for professional certificate generation.

## How It Works

### 1. Design Certificate Template
- Use Canva, Photoshop, or any design tool
- Create certificate with borders, seals, decorative elements
- Leave blank spaces for: Student Name, Date, Achievement, Signatures
- Export as high-quality PNG (1754x1240px for A4 landscape)
- Save to: `public/certificates/templates/`

### 2. System Overlays Text
- Intervention Image loads your template
- Adds student name, date, achievement dynamically
- Adds principal signature, school seal
- Converts to PDF for printing

### 3. Generate Certificates
- Admin selects students
- Chooses certificate type (Achievement, Completion, Excellence)
- System generates personalized certificates
- Download as PDF or bulk ZIP

## Certificate Template Examples

### Template 1: Achievement Certificate
- Gold border with school colors
- Official seal in corner
- Principal signature at bottom
- File: `achievement.png`

### Template 2: Completion Certificate
- Elegant border design
- School logo at top
- Date and signature lines
- File: `completion.png`

### Template 3: Excellence Certificate
- Premium design with decorative elements
- Multiple signature lines
- Gold/silver accents
- File: `excellence.png`

## Template Specifications

- **Size:** 1754x1240px (A4 landscape) or 1240x1754px (A4 portrait)
- **Format:** PNG with transparent or white background
- **Resolution:** 300 DPI for print quality
- **Color:** RGB or CMYK

## Folder Structure

```
public/
  certificates/
    templates/
      achievement.png
      completion.png
      excellence.png
    signatures/
      principal.png
      vice-principal.png
    seals/
      school-seal.png
```

## Next Steps

After installation completes, the certificate feature will be available in:
- More Features → Certificates
- Students → Generate Certificate (individual)
- Classes → Bulk Certificates

## Design Tips

1. **Use high contrast** - Dark text on light background
2. **Leave space** - 200px margins for text overlay
3. **Professional fonts** - System will use elegant fonts
4. **School branding** - Include logo, colors, motto
5. **Print-ready** - Design at actual print size

## Free Design Resources

- **Canva:** Search "certificate templates" (free)
- **Freepik:** Download certificate backgrounds
- **Flaticon:** Get decorative elements, seals
- **Google Fonts:** Download elegant fonts for system

---

**Ready to create stunning certificates!** 🎓
