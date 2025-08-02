<?php
// src/Services/ScheduleEngine.php

namespace App\Services;

use App\Models\{ClassModel, Subject, Teacher, Room, Curriculum, TimeSlot};
use Illuminate\Database\Capsule\Manager as DB;

class ScheduleEngine
{
    public static function generateSchedule($term, $options = [])
    {
        $subjects = Curriculum::where('term', $term)->first()->subjects;
        $teachers = Teacher::with('qualifiedSubjects')->get();
        $rooms = Room::all();
        $timeSlots = TimeSlot::all();

        $schedule = [];
        $conflicts = [];

        foreach ($subjects as $subject) {
            $qualifiedTeacher = $teachers->first(function ($teacher) use ($subject) {
                return $teacher->qualifiedSubjects->contains('id', $subject->id);
            });

            if (!$qualifiedTeacher) {
                $conflicts[] = "No qualified teacher for subject: {$subject->title}";
                continue;
            }

            $assignedRoom = $rooms->shift();
            if (!$assignedRoom) {
                $conflicts[] = "No available rooms for subject: {$subject->title}";
                continue;
            }

            $weeklyHours = $subject->weekly_hours;
            $slotsNeeded = ceil($weeklyHours);
            $availableSlots = $timeSlots->take($slotsNeeded);

            if ($availableSlots->count() < $slotsNeeded) {
                $conflicts[] = "Not enough time slots for subject: {$subject->title}";
                continue;
            }

            foreach ($availableSlots as $slot) {
                $existingClass = ClassModel::where('room_id', $assignedRoom->id)
                    ->where('day', self::getDayFromSlot($slot))
                    ->where('time_slot', $slot->id)
                    ->first();

                if ($existingClass && empty($options['allow_conflict'])) {
                    $conflicts[] = "Room conflict for subject: {$subject->title} at time slot: {$slot->id}";
                    continue;
                }

                $schedule[] = ClassModel::create([
                    'subject_id' => $subject->id,
                    'teacher_id' => $qualifiedTeacher->id,
                    'room_id' => $assignedRoom->id,
                    'day' => self::getDayFromSlot($slot),
                    'time_slot' => $slot->id,
                    'capacity' => $assignedRoom->capacity
                ]);
            }

            $rooms->push($assignedRoom);
        }

        return [
            'schedule' => $schedule,
            'conflicts' => $conflicts
        ];
    }

    private static function getDayFromSlot($slot)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $index = ($slot->id - 1) % count($days);
        return $days[$index];
    }
}
