<style>
.bpjs-new-badge { display: inline-flex; align-items: center; flex-shrink: 0; margin-left: 5px; padding: 2px 6px; border: 1px solid #a7f3d0; border-radius: 4px; background: #ecfdf5; color: #047857; font-size: 9px; font-weight: 700; line-height: 1.4; letter-spacing: .04em; vertical-align: middle; }
@media screen {
    /* Shared, restrained presentation for list, form, and detail screens. */
    .bpjs-workspace.bpjs-workspace > .bpjs-page-header { padding: 4px 0 20px; border: 0; border-bottom: 1px solid #e2e8f0; border-radius: 0; box-shadow: none; background: transparent; }
    .bpjs-workspace.bpjs-workspace .bpjs-page-header h1 { font-size: 22px; font-weight: 650; letter-spacing: -.025em; }
    .bpjs-workspace.bpjs-workspace .bpjs-page-header p { font-size: 13px; color: #64748b; }
    .bpjs-workspace.bpjs-workspace .bpjs-page-header > div:first-child > .w-10,
    .bpjs-workspace.bpjs-workspace .bpjs-heading-icon { display: none; }
    .bpjs-form-workspace #pranota-form > .bg-white { border-radius: 10px; box-shadow: none; border-color: #e2e8f0; }
    .bpjs-form-workspace #pranota-form > .bg-white > div:first-child { background: #fff; }
    .bpjs-form-workspace h2 { font-weight: 600; }
    .bpjs-form-workspace h2 ~ span { color: #64748b; }
    .bpjs-form-workspace #pranota-form > .grid { gap: 0; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; overflow: hidden; }
    .bpjs-form-workspace #pranota-form > .grid > div { border: 0; border-right: 1px solid #e2e8f0; border-radius: 0; box-shadow: none; padding: 16px 20px; background: transparent; }
    .bpjs-form-workspace #pranota-form > .grid > div:last-child { border-right: 0; background: #f0fdfa; }
    .bpjs-form-workspace #pranota-form > .grid > div > div:first-child { display: none; }
    .bpjs-form-workspace #pranota-form > .grid p:first-child { text-transform: none; letter-spacing: normal; font-weight: 500; color: #64748b; }
    .bpjs-form-workspace #pranota-form > .grid p:last-child { font-family: inherit; font-variant-numeric: tabular-nums; font-size: 18px; }
    .bpjs-extra-actions { position: relative; flex-shrink: 0; }
    .bpjs-extra-actions summary { cursor: pointer; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 7px; font-size: 12px; color: #475569; background: #fff; }
    .bpjs-extra-actions[open] > div { display: flex; flex-direction: column; align-items: stretch; gap: 8px; padding: 12px; margin-top: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; }
    .bpjs-extra-actions button { box-shadow: none; }
    .bpjs-form-workspace #bpjs-employee-picker { padding: 20px 24px; background: #fff; }
    .bpjs-picker-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; max-width: 800px; }
    .bpjs-picker-fields label { display: block; margin-bottom: 6px; }
    .bpjs-form-workspace .bpjs-picker-fields input,
    .bpjs-form-workspace .bpjs-picker-fields select { width: 100%; min-height: 40px; margin: 0; font-size: 13px; }
    #employee-picker-list:not(:empty) { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 16px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 4px 12px; background: #f8fafc; }
    #employee-picker-list > label { padding: 10px 0; margin: 0; border-bottom: 1px solid #e2e8f0; font-size: 12px; cursor: pointer; }
    #employee-picker-list input { flex-shrink: 0; border-radius: 4px; accent-color: #0d9488; }
    .bpjs-form-workspace #employee-picker-add { background: #0f766e; min-height: 40px; padding: 10px 16px; }
    .bpjs-form-workspace #employee-picker-add:hover:enabled { background: #115e59; }
    .bpjs-form-workspace #employee-picker-add:disabled { cursor: not-allowed; }
    .bpjs-form-workspace #tabel-detail thead th { font-weight: 600; letter-spacing: normal; }
    .bpjs-form-workspace #tabel-detail tbody tr:hover { background: #f8fafc; }
    @media (max-width: 640px) {
        .bpjs-workspace.bpjs-workspace > .bpjs-page-header { padding: 0 0 16px; }
        .bpjs-picker-fields, #employee-picker-list:not(:empty) { grid-template-columns: minmax(0, 1fr); }
        .bpjs-form-workspace #bpjs-employee-picker { padding: 16px; }
        .bpjs-form-workspace #pranota-form > .grid > div { border-right: 0; border-bottom: 1px solid #e2e8f0; }
        .bpjs-form-workspace #employee-picker-add { width: 100%; }
    }
    .bpjs-workspace { color: #334155; }
    .bpjs-workspace > .bpjs-page-header { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px; padding: 24px; margin-bottom: 24px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 1px 3px rgb(15 23 42 / .03); }
    .bpjs-page-header h1 { font-size: 24px; line-height: 1.35; letter-spacing: -.025em; color: #0f172a; }
    .bpjs-page-header > div:first-child { margin-bottom: 0; }
    .bpjs-page-header > div:last-child { display: flex; flex-wrap: wrap; gap: 8px; }
    .bpjs-page-header > div:last-child > * { margin-left: 0; }
    .bpjs-workspace .bpjs-heading-icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; margin-right: 10px; border-radius: 12px; background: #f0fdfa; color: #0d9488; font-size: 19px; vertical-align: middle; }
    .bpjs-workspace .bg-white.border.rounded-lg { border-radius: 14px; border-color: #e2e8f0; }
    .bpjs-workspace .bg-white.border.shadow-sm { box-shadow: 0 1px 3px rgb(15 23 42 / .03); }
    .bpjs-workspace input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]), .bpjs-workspace select, .bpjs-workspace textarea { border-radius: 8px; }
    .bpjs-workspace input:focus-visible, .bpjs-workspace select:focus-visible, .bpjs-workspace textarea:focus-visible { outline: 2px solid #14b8a6; outline-offset: 2px; }
    .bpjs-workspace a:focus-visible, .bpjs-workspace button:focus-visible { outline: 2px solid #0d9488; outline-offset: 3px; }
    .bpjs-workspace table { font-variant-numeric: tabular-nums; }
    .bpjs-list-table { min-width: 1180px; }
    .bpjs-list-table thead th { padding-top: 15px; padding-bottom: 15px; white-space: nowrap; font-size: 10px; letter-spacing: .05em; background: #f8fafc; color: #64748b; }
    .bpjs-list-table tbody td { padding-top: 16px; padding-bottom: 16px; vertical-align: middle; }
    .bpjs-list-table tbody tr:hover { background: #f0fdfa; }
    .bpjs-list-table td:last-child a, .bpjs-list-table td:last-child button { width: 32px; height: 32px; border-radius: 8px; }
    .bpjs-summary { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .bpjs-summary > div { min-width: 0; align-items: flex-start; }
    .bpjs-summary > div > div:first-child { width: 40px; height: 40px; border-radius: 12px; }
    .bpjs-summary > div > div:last-child { min-width: 0; }
    .bpjs-summary p { overflow-wrap: anywhere; }
    .bpjs-filter-grid { display: grid; grid-template-columns: minmax(220px, 2fr) repeat(3, minmax(130px, 1fr)); gap: 16px; padding: 20px; }
    .bpjs-filter-grid label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 600; color: #475569; }
    .bpjs-filter-grid label i { margin-right: 6px; color: #94a3b8; }
    .bpjs-filter-grid input, .bpjs-filter-grid select { width: 100%; min-height: 42px; border: 1px solid #cbd5e1; padding: 9px 12px; background: #fff; font-size: 13px; }
    @media (max-width: 1024px) { .bpjs-filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 640px) { .bpjs-workspace > .bpjs-page-header { padding: 18px; } .bpjs-page-header h1 { font-size: 20px; } .bpjs-summary, .bpjs-filter-grid { grid-template-columns: minmax(0, 1fr); } }
}
</style>
