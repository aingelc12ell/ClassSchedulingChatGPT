<?php

use PHPUnit\Framework\TestCase;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Services\ScheduleEngine;

class SchedulerTest extends TestCase
{
    protected ScheduleEngine $ScheduleEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ScheduleEngine = new ScheduleEngine();
    }

    public function testTeacherQualifiedForSubject()
    {
        $teacher = new Teacher(['id' => 1, 'name' => 'John Doe']);
        $subject = new Subject(['id' => 1, 'title' => 'Mathematics']);

        $teacher->subjects()->attach($subject);

        $this->assertTrue($teacher->isQualifiedForSubject($subject->id));
    }

    public function testScheduleNoStudentConflict()
    {
        $class1 = new ClassModel(['id' => 1, 'subject_id' => 1, 'room_id' => 1, 'day' => 'Monday', 'timeslot' => '09:00-10:00']);
        $class2 = new ClassModel(['id' => 2, 'subject_id' => 2, 'room_id' => 2, 'day' => 'Monday', 'timeslot' => '09:00-10:00']);

        $studentCurriculum = [1, 2]; // Student enrolled in both subjects

        $conflicts = $this->ScheduleEngine->checkStudentConflicts($studentCurriculum, [$class1, $class2]);

        $this->assertNotEmpty($conflicts);
    }

    public function testRoomExclusivity()
    {
        $class1 = new ClassModel(['id' => 1, 'room_id' => 1, 'day' => 'Tuesday', 'timeslot' => '10:00-11:00']);
        $class2 = new ClassModel(['id' => 2, 'room_id' => 1, 'day' => 'Tuesday', 'timeslot' => '10:00-11:00']);

        $conflicts = $this->ScheduleEngine->checkRoomConflicts([$class1, $class2]);

        $this->assertCount(1, $conflicts);
    }

    public function testManualOverrideAllowsConflict()
    {
        $class1 = new ClassModel(['id' => 1, 'subject_id' => 1, 'day' => 'Wednesday', 'timeslot' => '11:00-12:00', 'override_conflict' => true]);
        $class2 = new ClassModel(['id' => 2, 'subject_id' => 2, 'day' => 'Wednesday', 'timeslot' => '11:00-12:00']);

        $conflicts = $this->ScheduleEngine->checkStudentConflicts([1, 2], [$class1, $class2]);

        $this->assertEmpty($conflicts, 'Conflict should be ignored due to override.');
    }

}
