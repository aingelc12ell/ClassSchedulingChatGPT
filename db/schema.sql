-- Database: school_schedule

-- Drop tables if exist (for reset)
DROP TABLE IF EXISTS classes, time_slots, curriculums_subjects, curriculums, students, teachers_subjects, teachers, subjects, rooms;

-- Subjects
CREATE TABLE subjects
(
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(100)  NOT NULL,
    units        INT           NOT NULL,
    weekly_hours DECIMAL(4, 2) NOT NULL
);

-- Rooms
CREATE TABLE rooms
(
    id       INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(50) NOT NULL,
    capacity INT         NOT NULL
);

-- Teachers
CREATE TABLE teachers
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Teachers Qualified Subjects (Pivot)
CREATE TABLE teachers_subjects
(
    teacher_id INT,
    subject_id INT,
    PRIMARY KEY (teacher_id, subject_id),
    FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE CASCADE
);

-- Curriculums
CREATE TABLE curriculums
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    term VARCHAR(50)  NOT NULL
);

-- Curriculum Subjects (Pivot)
CREATE TABLE curriculums_subjects
(
    curriculum_id INT,
    subject_id    INT,
    PRIMARY KEY (curriculum_id, subject_id),
    FOREIGN KEY (curriculum_id) REFERENCES curriculums (id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE CASCADE
);

-- Students
CREATE TABLE students
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    curriculum_id INT,
    FOREIGN KEY (curriculum_id) REFERENCES curriculums (id)
);

-- Time Slots
CREATE TABLE time_slots
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    start_time TIME NOT NULL,
    end_time   TIME NOT NULL
);

-- Classes (Scheduled)
CREATE TABLE classes
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT,
    teacher_id INT,
    room_id    INT,
    day        VARCHAR(10) NOT NULL,
    time_slot  INT,
    capacity   INT         NOT NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects (id),
    FOREIGN KEY (teacher_id) REFERENCES teachers (id),
    FOREIGN KEY (room_id) REFERENCES rooms (id),
    FOREIGN KEY (time_slot) REFERENCES time_slots (id)
);

-- SEED DATA

INSERT INTO subjects (title, units, weekly_hours)
VALUES ('Mathematics', 3, 3.00),
       ('Science', 3, 3.00),
       ('English', 2, 2.00);

INSERT INTO rooms (name, capacity)
VALUES ('Room A', 30),
       ('Room B', 25);

INSERT INTO teachers (name)
VALUES ('John Doe'),
       ('Jane Smith');

INSERT INTO teachers_subjects (teacher_id, subject_id)
VALUES (1, 1),
       (1, 2),
       (2, 3);

INSERT INTO curriculums (name, term)
VALUES ('Grade 10 - Term 1', 'Term 1');

INSERT INTO curriculums_subjects (curriculum_id, subject_id)
VALUES (1, 1),
       (1, 2),
       (1, 3);

INSERT INTO students (name, curriculum_id)
VALUES ('Alice Brown', 1),
       ('Bob White', 1);

INSERT INTO time_slots (start_time, end_time)
VALUES ('09:00:00', '10:00:00'),
       ('10:00:00', '11:00:00'),
       ('11:00:00', '12:00:00');
