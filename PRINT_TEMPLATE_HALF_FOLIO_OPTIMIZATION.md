# PRINT TEMPLATE OPTIMIZATION - HALF FOLIO & PLAT INFORMATION

## Overview

Updated the permohonan print template to add plat (license plate) information, optimize for half folio (A5) printing, and changed the destination field display.

## Changes Made

### 1. Added Plat Information

**Location**: Memo Information Section (Right Column)

-   Added "Plat Nomor" field after "Krani" information
-   Displays `plat_nomor` from the permohonan data
-   Shows "-" if no plat number is available

### 2. Modified Destination Field

**Changed**: "Dari - Ke" → "Tujuan"

-   **Old Display**: `{{ $permohonan->dari ?? '-' }} - {{ $permohonan->ke ?? '-' }}`
-   **New Display**: `{{ $permohonan->ke ?? '-' }}`
-   Label changed from "Dari - Ke" to "Tujuan"
-   Now shows only the destination (ke) field

### 3. Half Folio Print Optimization

#### A. Page Size & Layout

-   **Print Media**: Set to A5 portrait orientation
-   **Margins**: Reduced to 10mm for A5 paper
-   **Container Width**: Optimized to 100% for better space usage

#### B. Font Size Reductions

-   **Body Font**: 12px → 11px (normal), 10px (print)
-   **Header H1**: 20px → 18px (normal), 16px (print)
-   **Header H2**: 16px → 14px (normal), 12px (print)
-   **Section Titles**: 14px → 12px
-   **Total Rows**: 14px → 12px
-   **Total Final**: 16px → 14px
-   **Table Cells**: Added 10px font-size
-   **Signature Boxes**: 10px (normal), 9px (print)

#### C. Spacing Optimizations

-   **Body Padding**: 20px → 10px (normal), 5px (print)
-   **Header Padding**: 15px → 10px (normal), 8px (print)
-   **Memo Info Padding**: 15px → 10px (normal), 8px (print)
-   **Content Section Padding**: 15px → 10px (normal), 8px (print)
-   **Info Row Margins**: 8px → 6px
-   **Table Padding**: 8px → 6px
-   **Signature Margins**: 40px → 20px (normal), 15px (print)
-   **Signature Height**: 60px → 50px (normal), 40px (print)

#### D. Responsive Print Styles

```css
@media print {
    @page {
        size: A5 portrait;
        margin: 10mm;
    }
    /* Additional print-specific optimizations */
}
```

## Current Layout Structure

### Memo Information Section

**Left Column:**

1. Nomor Memo
2. Tanggal
3. Kegiatan
4. Vendor

**Right Column:**

1. Supir
2. Krani
3. **Plat Nomor** ← New
4. **Tujuan** ← Modified (was "Dari - Ke")

### Print Specifications

-   **Paper Size**: A5 (148mm × 210mm)
-   **Orientation**: Portrait
-   **Margins**: 10mm all sides
-   **Font Family**: Arial, sans-serif
-   **Base Font Size**: 11px (screen), 10px (print)

## Benefits

### 1. Enhanced Information Display

-   **Plat Information**: Now includes vehicle license plate for better tracking
-   **Cleaner Destination**: Shows only relevant destination instead of route
-   **Better Space Usage**: Optimized field labels for clarity

### 2. Half Folio Compatibility

-   **Cost Effective**: Uses less paper (A5 vs A4)
-   **Portable**: Easier to handle and file
-   **Professional**: Maintains readability on smaller format
-   **Efficient**: Better paper utilization for memo documents

### 3. Improved Readability

-   **Proportional Scaling**: All elements scaled appropriately
-   **Consistent Spacing**: Maintained visual hierarchy
-   **Print-Optimized**: Specific styles for print media
-   **Space Efficient**: Maximum content in minimum space

## Usage Instructions

### Printing Setup

1. **Paper Size**: Set printer to A5 or Half Letter
2. **Orientation**: Portrait
3. **Margins**: Let CSS handle margins (10mm)
4. **Scale**: 100% (do not scale to fit)

### Browser Print Settings

-   **Destination**: Select appropriate printer
-   **Paper Size**: A5 or Half Letter
-   **Margins**: Default or Custom (10mm)
-   **Scale**: 100%
-   **Background Graphics**: Enable for better appearance

### Information Display

-   **Plat Nomor**: Automatically populated from permohonan data
-   **Tujuan**: Shows destination from 'ke' field
-   **All Fields**: Gracefully handle missing data with "-"

## Technical Implementation

### CSS Media Query

```css
@media print {
    @page {
        size: A5 portrait;
        margin: 10mm;
    }
    /* Responsive print styles */
}
```

### Field Updates

```html
<!-- Added Plat Information -->
<div class="info-row">
    <span class="info-label">Plat Nomor:</span>
    <span class="info-value">{{ $permohonan->plat_nomor ?? '-' }}</span>
</div>

<!-- Modified Destination -->
<div class="info-row">
    <span class="info-label">Tujuan:</span>
    <span class="info-value">{{ $permohonan->ke ?? '-' }}</span>
</div>
```

## Future Considerations

### Print Options

-   Consider adding print preference settings
-   Option to switch between A4 and A5 formats
-   Custom field selection for different memo types

### Layout Variations

-   Landscape orientation option for wider tables
-   Compact vs detailed view options
-   Multiple signature layout options

### Data Enhancement

-   Plat number validation and formatting
-   Destination autocomplete for consistency
-   Route optimization integration

## Conclusion

The updated print template successfully provides:

1. **Enhanced Information** with plat number and cleaner destination display
2. **Optimized Layout** for half folio printing with proper scaling
3. **Professional Appearance** maintained across different paper sizes
4. **Cost Efficiency** through reduced paper usage
5. **Better Portability** with compact A5 format

The template now offers a more comprehensive and efficient solution for memo printing while maintaining all essential information in a compact, professional format.
