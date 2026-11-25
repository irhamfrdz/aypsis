const assert = require('node:assert/strict');
const test = require('node:test');
require('../public/js/pranota-bpjs-new-employee.js');

test('NEW follows the selected period, including year boundaries', () => {
    const employee = { created_at: '2026-11-25T09:00:00.000000Z' };
    assert.equal(BpjsNewEmployee.matches(employee, '11', '2026'), true);
    assert.equal(BpjsNewEmployee.matches(employee, '10', '2026'), false);
    assert.equal(BpjsNewEmployee.matches(employee, '11', '2025'), false);
    assert.equal(BpjsNewEmployee.matches({ created_at: '2025-12-31 23:59:59' }, 1, 2026), false);
});

test('older snapshots without creation dates do not receive NEW', () => {
    for (const employee of [undefined, {}, { created_at: null }, { created_at: 'invalid' }]) {
        assert.equal(BpjsNewEmployee.matches(employee, 11, 2026), false);
    }
});
