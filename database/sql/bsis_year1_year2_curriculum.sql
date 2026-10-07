-- BSIS (CCICT) curriculum: First and Second Year, both semesters.
-- course_total_hours is a generated column, so it is intentionally omitted.
-- Prerequisites are not stored by the current course table schema.
-- Existing BSIS course codes are preserved; this inserts only missing codes.

BEGIN;

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
    d.dept_college_id,
    d.dept_id,
    v.course_code,
    v.course_name,
    v.course_units,
    v.lecture_hours,
    v.lab_hours,
    v.year_level,
    v.semester,
    TRUE,
    NOW(),
    NOW()
FROM (VALUES
    -- First Year, First Semester (22 units; 18 lecture + 12 lab hours)
    ('GEC-RPH', 'Readings in Philippine History', 3::numeric, 3::numeric, 0::numeric, 1, 1),
    ('GEC-MMW', 'Mathematics in the Modern World', 3, 3, 0, 1, 1),
    ('GEE-TEM', 'The Entrepreneurial Mind', 3, 3, 0, 1, 1),
    ('CC 111', 'Introduction to Computing', 3, 2, 3, 1, 1),
    ('CC 112', 'Computer Programming 1 (Lec)', 2, 2, 0, 1, 1),
    ('CC 112 L', 'Computer Programming 1 (Lab)', 3, 0, 9, 1, 1),
    ('PATHFit1', 'Physical Activities Towards Health and Fitness 1: Movement Competency Training', 2, 2, 0, 1, 1),
    ('NSTP 1', 'National Service Training Program 1 (CWTS 1/LTS 1/ROTC 1)', 3, 3, 0, 1, 1),

    -- First Year, Second Semester (25 units; 22 lecture + 9 lab hours)
    ('GEC-PC', 'Purposive Communication', 3, 3, 0, 1, 2),
    ('GEC-STS', 'Science, Technology and Society', 3, 3, 0, 1, 2),
    ('GEC-US', 'Understanding the Self', 3, 3, 0, 1, 2),
    ('CC 123', 'Computer Programming 2 (Lec)', 2, 2, 0, 1, 2),
    ('CC 123 L', 'Computer Programming 2 (Lab)', 3, 0, 9, 1, 2),
    ('PC 121', 'Fundamentals of Information Systems', 3, 3, 0, 1, 2),
    ('PC 122', 'Organization and Management Concepts', 3, 3, 0, 1, 2),
    ('PATHFit2', 'Physical Activities Towards Health and Fitness 2: Exercise-based Fitness Activities', 2, 2, 0, 1, 2),
    ('NSTP 2', 'National Service Training Program 2 (CWTS 2/LTS 2/ROTC 2)', 3, 3, 0, 1, 2),

    -- Second Year, First Semester (25 units; 21 lecture + 12 lab hours)
    ('GEC-E', 'Ethics', 3, 3, 0, 2, 1),
    ('GEE-ES', 'Environmental Science', 3, 3, 0, 2, 1),
    ('GEC-LWR', 'Life and Works of Rizal', 3, 3, 0, 2, 1),
    ('CC 214', 'Data Structures and Algorithms (Lec)', 2, 2, 0, 2, 1),
    ('CC 214 L', 'Data Structures and Algorithms (Lab)', 3, 0, 9, 2, 1),
    ('PC 213', 'Professional Issues in Information Systems', 3, 3, 0, 2, 1),
    ('PC 214', 'Financial Management', 3, 3, 0, 2, 1),
    ('AP 1', 'Computer Architecture and Organization', 3, 2, 3, 2, 1),
    ('PATHFit3', 'Physical Activities Towards Health and Fitness 3: Dance/Sports/Martial Arts/Group Exercise/Outdoor and Adventure Activities', 2, 2, 0, 2, 1),

    -- Second Year, Second Semester (25 units; 20 lecture + 15 lab hours)
    ('GEC-TCW', 'The Contemporary World', 3, 3, 0, 2, 2),
    ('GEE-GPSP', 'Gender and Society with Peace Studies', 3, 3, 0, 2, 2),
    ('GEC-PEE', 'People and the Earth''s Ecosystems', 3, 3, 0, 2, 2),
    ('CC 225', 'Information Management (Lec)', 2, 2, 0, 2, 2),
    ('CC 225 L', 'Information Management (Lab)', 3, 0, 9, 2, 2),
    ('PC 225', 'IT Infrastructure and Network Technologies', 3, 2, 3, 2, 2),
    ('PC 226', 'Business Process Management', 3, 3, 0, 2, 2),
    ('AP 2', 'Web I: Client Development', 3, 2, 3, 2, 2),
    ('PATHFit4', 'Physical Activities Towards Health and Fitness 4: Dance/Sports/Martial Arts/Group Exercise/Outdoor and Adventure Activities', 2, 2, 0, 2, 2)
) AS v(course_code, course_name, course_units, lecture_hours, lab_hours, year_level, semester)
JOIN public.department AS d
  ON UPPER(TRIM(d.dept_code)) = 'BSIS'
JOIN public.college AS c
  ON c.college_id = d.dept_college_id
 AND UPPER(TRIM(c.college_code)) = 'CCICT'
WHERE NOT EXISTS (
    SELECT 1
    FROM public.course AS existing
    WHERE existing.course_dept_id = d.dept_id
      AND UPPER(TRIM(existing.course_code)) = UPPER(TRIM(v.course_code))
);

COMMIT;
