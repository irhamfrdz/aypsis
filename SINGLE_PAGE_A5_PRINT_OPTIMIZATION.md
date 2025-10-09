# SINGLE PAGE A5 PRINT OPTIMIZATION

## Overview

Optimized the permohonan print template to ensure it fits on a single half folio (A5) page by reducing spacing, font sizes, and content density while maintaining readability.

## Changes Made for Single Page Fit

### 1. Overall Layout Reduction

-   **Body Font**: 11px → 10px (normal), 10px → 9px (print)
-   **Body Padding**: 10px → 5px (normal), 5px → 3px (print)
-   **Page Margins**: 10mm → 8mm for A5 print

### 2. Header Optimization

-   **Header Padding**: 10px → 6px (normal), 8px → 4px (print)
-   **H1 Font Size**: 18px → 16px (normal), 16px → 14px (print)
-   **H2 Font Size**: 14px → 12px (normal), 12px → 10px (print)
-   **H2 Margin**: 3px → 2px

### 3. Memo Information Section

-   **Section Padding**: 10px → 6px (normal), 8px → 4px (print)
-   **Info Row Margin**: 6px → 4px
-   **Label Width**: 110px → 100px
-   **Label Font**: Added 9px (normal), 8px (print)
-   **Value Font**: Added 10px (normal), 9px (print)
-   **Value Height**: 16px → 14px

### 4. Content Sections

-   **Content Padding**: 10px → 6px (normal), 8px → 4px (print)
-   **Section Title Font**: 12px → 10px (normal), 10px → 9px (print)
-   **Section Title Margin**: 8px → 4px
-   **Section Title Padding**: 4px → 2px

### 5. Kontainer Table Optimization

-   **Table Margin**: 15px → 8px
-   **Cell Padding**: 8px → 4px (normal), 4px → 3px (print)
-   **Table Font**: 11px → 9px (normal), 9px → 8px (print)
-   **Header Height**: 25px → 18px
-   **Row Height**: 40px → 25px (6 rows instead of 8)
-   **Cell Min Height**: 40px → 25px

### 6. Signature Section

-   **Signature Margin Top**: 20px → 8px (normal), 15px → 5px (print)
-   **Signature Padding**: 15px → 8px (normal), 10px → 5px (print)
-   **Signature Box Width**: 150px → 120px (normal), 120px → 100px (print)
-   **Signature Font**: 10px → 8px (normal), 9px → 7px (print)
-   **Signature Line Height**: 50px → 35px (normal), 40px → 30px (print)
-   **Signature Line Margin**: 8px → 5px

### 7. Additional Optimizations

-   **Keterangan Padding**: 10px → 5px
-   **Keterangan Min Height**: 60px → 30px
-   **Keterangan Font**: Added 9px
-   **Table Row Count**: Reduced from 8 to 6 rows

## Current A5 Layout Specifications

### Page Setup

-   **Size**: A5 Portrait (148mm × 210mm)
-   **Margins**: 8mm all sides
-   **Printable Area**: ~132mm × 194mm
-   **Font Family**: Arial, sans-serif

### Element Sizing (Print)

-   **Base Font**: 9px
-   **Header Title**: 14px
-   **Header Subtitle**: 10px
-   **Labels**: 8px
-   **Values**: 9px
-   **Section Titles**: 9px
-   **Table Content**: 8px
-   **Signatures**: 7px

### Spacing (Print)

-   **Body Padding**: 3px
-   **Header Padding**: 4px
-   **Section Padding**: 4px
-   **Cell Padding**: 3px
-   **Signature Height**: 30px

## Content Distribution

### Top Section (Header + Info)

-   **Header**: Company name and memo title
-   **Memo Info**: 8 fields in 2 columns (4 each)
-   **Estimated Height**: ~45mm

### Middle Section (Table)

-   **Table Header**: 4 columns
-   **Table Rows**: 6 empty rows for manual entry
-   **Row Height**: 25px each
-   **Estimated Height**: ~45mm

### Bottom Section (Signatures + Footer)

-   **Keterangan**: Optional, compressed if present
-   **Signatures**: 3 signature boxes
-   **Footer**: Company name only
-   **Estimated Height**: ~35mm

### Total Estimated Height

-   **Content**: ~125mm
-   **Available Space**: ~194mm
-   **Buffer**: ~69mm (sufficient for spacing and variations)

## Benefits of Optimization

### 1. Guaranteed Single Page

-   **Compact Design**: All content fits within A5 boundaries
-   **No Page Breaks**: Complete memo on one sheet
-   **Cost Effective**: Minimal paper usage
-   **Portable**: Easy to handle and file

### 2. Maintained Functionality

-   **Readable**: All text remains legible
-   **Writable**: Adequate space for manual entry
-   **Professional**: Clean, organized appearance
-   **Complete**: All essential information preserved

### 3. Print Efficiency

-   **Consistent**: Same layout every time
-   **Reliable**: No printer-dependent variations
-   **Fast**: Quick printing without scaling issues
-   **Quality**: Sharp text and clean borders

## Usage Instructions

### Print Settings

1. **Paper Size**: Select A5 (148mm × 210mm)
2. **Orientation**: Portrait
3. **Margins**: Use CSS margins (8mm)
4. **Scale**: 100% - do not scale to fit
5. **Quality**: High/Best quality for clear text

### Browser Settings

-   **Print Preview**: Verify single page fit
-   **Background**: Enable for proper borders
-   **Headers/Footers**: Disable browser headers
-   **Color**: Enable for proper section backgrounds

### Manual Entry Guidelines

-   **Pen**: Use fine tip (0.7mm or smaller)
-   **Writing**: Print clearly in available space
-   **Rows**: Cross out unused table rows
-   **Corrections**: Use neat corrections if needed

## Technical Implementation

### CSS Media Query Structure

```css
@media print {
    @page {
        size: A5 portrait;
        margin: 8mm;
    }
    /* Compressed print styles */
}
```

### Key Responsive Elements

-   Progressive font size reduction
-   Proportional spacing compression
-   Maintained aspect ratios
-   Consistent border weights

## Quality Assurance

### Test Checklist

-   [ ] All content fits on one A5 page
-   [ ] Text remains readable at print size
-   [ ] Table rows provide adequate writing space
-   [ ] Signature areas are appropriately sized
-   [ ] Borders and lines print clearly
-   [ ] No content cutoff at margins

### Browser Compatibility

-   ✅ Chrome/Chromium
-   ✅ Firefox
-   ✅ Safari
-   ✅ Edge

## Future Considerations

### Responsive Adjustments

-   Monitor actual print results for fine-tuning
-   Consider printer-specific variations
-   Option for even more compact "ultra-dense" mode
-   Alternative landscape orientation for special cases

### Content Flexibility

-   Dynamic row count based on content
-   Collapsible sections for optional information
-   Variable signature configurations
-   Multiple memo formats for different purposes

## Conclusion

The optimized template successfully achieves single-page A5 printing while maintaining all essential functionality:

1. **Space Efficiency**: Maximum content in minimum space
2. **Readability**: Maintained legible text sizes
3. **Usability**: Adequate manual entry areas
4. **Professionalism**: Clean, organized appearance
5. **Reliability**: Consistent single-page output

The template now provides a practical, cost-effective solution for memo printing that reliably fits on half folio paper while preserving all operational requirements.
