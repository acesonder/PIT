# Image Requirements for PIT Count Application

This document describes the images needed for the PIT Count application. Generate or source these images as described below.

## Required Images

### 1. OUTSINC Logo Icon
**File**: `logo-icon.png`  
**Size**: 200x200px (minimum), transparent background preferred  
**Description**: 
- Main organizational logo for OUTSINC
- Should be simple, professional, and recognizable
- Used in header across all pages
- Colors: Blue (#2c5aa0) and/or Orange (#e8732a) to match site theme
- Could incorporate initials "OI" or "OUTSINC" creatively

### 2. Hero/Landing Page Image
**File**: `hero-community-outreach.jpg`  
**Size**: 1600x600px (minimum)  
**Description**:
- **Scene**: Downtown Cobourg, Ontario (recognizable landmarks like Victoria Hall or waterfront)
- **People**: Diverse group of 4-6 people including:
  - 2-3 outreach workers (wearing casual professional attire, clipboards/tablets)
  - 2-3 individuals experiencing homelessness (various ages, respectfully depicted)
- **Mood**: Compassionate, hopeful, community-focused
- **Setting**: Daytime, outdoor urban setting, showing connection and conversation
- **Colors**: Natural, warm tones that complement the purple gradient background
- **Composition**: Horizontal, with space for text overlay if needed

### 3. What is PIT? Illustration
**File**: `pit-info-graphic.png`  
**Size**: 800x600px  
**Description**:
- Infographic-style illustration explaining Point-in-Time counts
- Could show:
  - Calendar highlighting a specific date
  - Map of Northumberland County with pins
  - People icons representing counting
  - Data/chart elements showing results
- **Style**: Clean, modern, icons-based
- **Colors**: Match site theme (blues, purples, orange accents)

### 4. Warming Room Planning Image
**File**: `warming-room-concept.jpg`  
**Size**: 1200x800px  
**Description**:
- **Scene**: Interior of a welcoming community space/warming room
- **Elements**: 
  - Comfortable seating
  - Warm lighting
  - People of diverse backgrounds feeling safe and comfortable
  - Coffee/food service area visible
  - Community bulletin board
- **Mood**: Warm, safe, inclusive, dignified
- **Setting**: Interior, well-lit, clean, respectful portrayal

### 5. Community Services Icon Set
**Files**: `icon-shelter.svg`, `icon-food.svg`, `icon-health.svg`, `icon-outreach.svg`  
**Size**: 64x64px each, SVG format preferred  
**Description**:
- Simple line icons representing:
  - **Shelter**: House/roof icon
  - **Food**: Food bank/meal icon
  - **Health**: Medical cross or heart with pulse
  - **Outreach**: People/hands helping icon
- **Style**: Consistent line-weight, minimalist
- **Colors**: Single color (can be styled with CSS)

### 6. Assessment Type Icons
**Files**: `icon-short.svg`, `icon-medium.svg`, `icon-hard.svg`  
**Size**: 128x128px each  
**Description**:
- Visual representations for each assessment type:
  - **SHORT**: Single page/clipboard icon
  - **MEDIUM**: Multiple pages/form icon
  - **HARD**: Thick document/detailed form icon
- **Style**: Modern, consistent with other icons
- **Colors**: Theme colors with gradients

### 7. Background Patterns (Optional)
**Files**: `pattern-dots.png`, `pattern-waves.png`  
**Size**: Tileable (256x256px)  
**Description**:
- Subtle background patterns for sections
- Very low opacity usage
- Complementary to main color scheme

## Image Placement Reference

- **Landing Page Hero**: Top of index.html (currently has placeholder description)
- **Logo**: Header of all pages
- **Icons**: Throughout forms and information sections
- **Background**: Hero sections and feature areas

## Design Guidelines

### Color Palette (from CSS)
- Primary Blue: #2c5aa0
- Secondary Blue: #4a90e2
- Accent Orange: #e8732a
- Success Green: #28a745
- Purple Gradient: #667eea to #764ba2

### Photography Style
- **Respectful**: Always portray people experiencing homelessness with dignity
- **Authentic**: Real community feel, not overly staged
- **Diverse**: Show age, gender, and ethnic diversity
- **Local**: When possible, use Cobourg/Northumberland locations
- **Hopeful**: Focus on solutions and community support, not despair

### Accessibility
- All images should have alt text (add to HTML)
- Sufficient contrast for icons used as functional elements
- Don't rely solely on color to convey information

## Stock Photo Resources

If generating/photographing custom images isn't immediately possible, consider these resources:

1. **Unsplash** (unsplash.com) - Free stock photos
   - Search: "community outreach", "diverse people", "helping", "warmth"
   
2. **Pexels** (pexels.com) - Free stock photos
   - Search: "social worker", "community center", "helping hands"

3. **Flaticon** (flaticon.com) - Free icons
   - Search: service-related icons

4. **Canva** (canva.com) - Create custom graphics and logos
   - Use templates for non-profit organizations

## Implementation

Once images are ready:

1. Place all images in the `/images/` directory
2. Update HTML files to replace placeholder text with actual `<img>` tags
3. Add appropriate alt text for accessibility
4. Optimize images for web (compress, appropriate format)
5. Test responsive behavior on mobile devices

## Attribution

If using stock photos or licensed images, ensure proper attribution is included in the footer or about page as required by the license.
