-- Insert the BSIS Fourth Year, Second Semester OJT course in Supabase (PostgreSQL).
-- Units, lecture hours, and lab hours start at zero. course_total_hours is generated
-- from lecture + lab hours, so it will also be zero and is intentionally omitted.
-- This script resets an existing matching OJT row to zero; otherwise it inserts one.

BEGIN;

UPDATE public.course AS existing
SET course_name = 'On-the-Job Training (OJT)',
    course_units = 0,
    course_lecture_hours = 0,
    course_lab_hours = 0,
    course_year_level = 4,
    course_semester = 2,
    course_is_active = TRUE,
    course_updated_at = NOW()
FROM public.department AS department
JOIN public.college AS college
  ON college.college_id = department.dept_college_id
 AND UPPER(TRIM(college.college_code)) = 'CCICT'
WHERE UPPER(TRIM(department.dept_code)) = 'BSIS'
  AND existing.course_dept_id = department.dept_id
  AND UPPER(TRIM(existing.course_code)) = 'PC 4215';

INSERT INTO public.course (
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
    course_is_active,
    course_created_at,
    course_updated_at
)
SELECT
    gen_random_uuid(),
    department.dept_college_id,
    department.dept_id,
    'PC 4215',
    'On-the-Job Training (OJT)',
    0,
    0,
    0,
    4,
    2,
    TRUE,
    NOW(),
    NOW()
FROM public.department AS department
JOIN public.college AS college
  ON college.college_id = department.dept_college_id
 AND UPPER(TRIM(college.college_code)) = 'CCICT'
WHERE UPPER(TRIM(department.dept_code)) = 'BSIS'
  AND NOT EXISTS (
      SELECT 1
      FROM public.course AS existing
      WHERE existing.course_dept_id = department.dept_id
        AND UPPER(TRIM(existing.course_code)) = 'PC 4215'
  );

COMMIT;
