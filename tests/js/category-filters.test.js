import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';
import { fileURLToPath } from 'node:url';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const allCourses = [
    { id: 'a', code: 'A101', dept: 'dept-a', year: '2', sem: '1', cat: 'General' },
    { id: 'b', code: 'B201', dept: 'dept-a', year: '3', sem: '1', cat: 'General' },
    { id: 'c', code: 'C202', dept: 'dept-a', year: '2', sem: '2', cat: 'General' },
    { id: 'd', code: 'D101', dept: 'dept-b', year: '2', sem: '1', cat: 'General' },
    { id: 'e', code: 'E402', dept: 'dept-b', year: '4', sem: '2', cat: 'General' },
    { id: 'f', code: 'F101', dept: 'dept-a', year: '2', sem: '1', cat: 'Laboratory' },
];

function createPageContext(scriptPath) {
    const section = {
        options: [{ dataset: { deptId: 'dept-a', yearLevel: '2' } }],
        selectedIndex: 0,
    };
    const department = { value: 'dept-a' };
    const semesterFilter = {
        value: 'semester-one',
        selectedOptions: [{ dataset: { courseSemester: '1' } }],
    };
    const elements = new Map([
        ['add-section', section],
        ['edit-section', section],
        ['add-department', department],
        ['edit-department', department],
        ['filter-semester', semesterFilter],
    ]);

    const document = {
        addEventListener() {},
        querySelectorAll() { return []; },
        getElementById(id) { return elements.get(id) ?? null; },
        createElement() { return { dataset: {} }; },
    };
    const window = {
        ALL_SUBJECTS_LIST: allCourses,
        ACTIVE_COURSE_SEMESTER: 1,
        PBS_SELECTED_DATE: null,
        PBT_SELECTED_DATE: null,
        PBT_SELECTED_TEACHER: null,
    };
    const context = vm.createContext({
        document,
        window,
        getSelectedCourseSemester() { return window.ACTIVE_COURSE_SEMESTER; },
    });
    const source = fs.readFileSync(path.join(projectRoot, scriptPath), 'utf8');
    const optionFilter = source.match(/function populateSubjectOptions\([\s\S]*?\n}\n/);
    assert.ok(optionFilter, `Could not find populateSubjectOptions() in ${scriptPath}`);
    vm.runInContext(optionFilter[0], context, { filename: scriptPath });

    return context;
}

for (const page of ['pbs', 'pbt']) {
    test(`${page.toUpperCase()} filters courses by department, year, and semester without a category`, () => {
        const context = createPageContext(`public/js/${page}.js`);
        const selected = [];
        const select = {
            set innerHTML(value) { this.html = value; selected.length = 0; },
            appendChild(option) { selected.push(option.value); },
        };

        context.populateSubjectOptions(
            select,
            '',
            'add-section',
            'add-department',
            false,
        );

        assert.deepEqual(selected, ['a', 'f']);
    });

    test(`${page.toUpperCase()} shows all matching-category courses across departments, years, and semesters`, () => {
        const context = createPageContext(`public/js/${page}.js`);
        const selected = [];
        const select = {
            set innerHTML(value) { this.html = value; selected.length = 0; },
            appendChild(option) { selected.push(option.value); },
        };

        context.populateSubjectOptions(
            select,
            'General',
            'add-section',
            'add-department',
            false,
        );

        assert.deepEqual(selected, ['a', 'b', 'c', 'd', 'e']);
    });
}
