-- BSIT - Third Year, First Semester
-- Department: Bachelor of Science in Information Technology
-- Department ID: 8de117bd-ed91-45dc-9ad4-f75eede0cb38
-- College ID: c5d5cb3b-eda7-4f13-99b1-f7602a749b30
-- Review course codes, names, units, and hours before running.

INSERT INTO course (
    course_id,
    course_college_id,
    course_dept_id,
    course_code,
    course_name,
    course_units,
    course_lecture_hours,
    course_lab_hours,
    course_year_level,
    course_semester,
    course_is_active
)
VALUES
(
    gen_random_uuid(),
    'c5d5cb3b-eda7-4f13-99b1-f7602a749b30',
    '8de117bd-ed91-45dc-9ad4-f75eede0cb38',
    'GEE-FE',
    'Functional English',
    3,
    3,
    0,
    3,
    '1st Semester',
    true
),
(
    gen_random_uuid(),
    'c5d5cb3b-eda7-4f13-99b1-f7602a749b30',
    '8de117bd-ed91-45dc-9ad4-f75eede0cb38',
    'PC 315',
    'Networking 2 (Lec)',
    2,
    2,
    0,
    3,
    '1st Semester',
    true
),
(
    gen_random_uuid(),
    'c5d5cb3b-eda7-4f13-99b1-f7602a749b30',
    '8de117bd-ed91-45dc-9ad4-f75eede0cb38',
    'PC 315 L',
    'Networking 2 (Lab)',
    3,
    0,
    9,
    3,
    '1st Semester',
    true
),
(
    gen_random_uuid(),
    'c5d5cb3b-eda7-4f13-99b1-f7602a749b30',
    '8de117bd-ed91-45dc-9ad4-f75eede0cb38',
    'PC 316',
    'Systems Integration and Architecture 1',
    3,
    2,
    3,
    3,
    '1st Semester',
    true
),
(
    gen_random_uuid(),
    'c5d5cb3b-eda7-4f13-99b1-f7602a749b30',
    '8de117bd-ed91-45dc-9ad4-f75eede0cb38',
    'PC 317',
    'Introduction to Human Computer Interaction',
    3,
    2,
    3,
    3,
    '1st Semester',
    true
),
(
    gen_random_uuid(),
    'c5d5cb3b-eda7-4f13-99b1-f7602a749b30',
    '8de117bd-ed91-45dc-9ad4-f75eede0cb38',
    'PC 318',
    'Database Management Systems',
    3,
    2,
    3,
    3,
    '1st Semester',
    true
),
(
    gen_random_uuid(),
    'c5d5cb3b-eda7-4f13-99b1-f7602a749b30',
    '8de117bd-ed91-45dc-9ad4-f75eede0cb38',
    'CC 316',
    'Applications Development and Emerging Technologies',
    3,
    2,
    3,
    3,
    '1st Semester',
    true
);
