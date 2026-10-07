-- Correct Third Year, First Semester BSIS courses in Supabase (PostgreSQL).
-- Total hours remain generated from lecture + lab hours; this does not alter the schema.
-- Run in the Supabase SQL Editor. Only existing matching courses are updated.

BEGIN;

WITH corrected_courses(course_code, course_name, units, lecture_hours, lab_hours) AS (
    VALUES
        ('PC 317',   'Systems Analysis and Design (Lec)',  2::numeric, 2::numeric, 0::numeric),
        ('PC 317 L', 'Systems Analysis and Design (Lab)',  3,          0,          9),
        ('PC 318',   'Enterprise Architecture',           3,          2,          3),
        ('PC 319',   'Evaluation of Business Performance', 3,          3,          0),
        ('PC 3110',  'Quantitative Methods',               3,          3,          0),
        ('AP 3',     'Web II Development',                 3,          2,          3),
        ('AP 4',     'Database Administration DBMS',       3,          2,          3)
)
UPDATE public.course AS course
SET course_name = corrected_courses.course_name,
    course_units = corrected_courses.units,
    course_lecture_hours = corrected_courses.lecture_hours,
    course_lab_hours = corrected_courses.lab_hours,
    course_year_level = 3,
    course_semester = 1,
    course_updated_at = NOW()
FROM corrected_courses
JOIN public.department AS department
  ON UPPER(TRIM(department.dept_code)) = 'BSIS'
JOIN public.college AS college
  ON college.college_id = department.dept_college_id
 AND UPPER(TRIM(college.college_code)) = 'CCICT'
WHERE course.course_dept_id = department.dept_id
  AND UPPER(TRIM(course.course_code)) = UPPER(TRIM(corrected_courses.course_code));

COMMIT;
