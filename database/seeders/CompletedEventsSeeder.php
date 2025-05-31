<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CompletedEventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an admin user
        $admin = User::whereIn('user_role', ['superadmin', 'officer'])->first();

        if (!$admin) {
            return;
        }

        // Create sample completed events
        $events = [
            [
                'title' => 'Jamming on an Old Saya',
                'description' => 'A cultural music event featuring traditional Filipino instruments and music styles. Join us for an evening of cultural appreciation and musical exploration.',
                'event_type' => 'Cultural',
                'start_date_time' => Carbon::parse('2023-05-02 17:30:00'),
                'end_date_time' => Carbon::parse('2023-05-02 19:30:00'),
                'location' => 'ICS Auditorium',
                'location_details' => 'Main Campus, Building A',
                'status' => 'completed',
                'notes' => 'Open to all students',
                'created_by' => $admin->id,
                'image_path' => 'img/events/jamming.jpg',
            ],
            [
                'title' => 'Jamming on an Old Saya - Session 2',
                'description' => 'Second session of our cultural music event featuring traditional Filipino instruments and music styles. This follow-up session will dive deeper into the techniques and history.',
                'event_type' => 'Cultural',
                'start_date_time' => Carbon::parse('2023-05-05 14:00:00'),
                'end_date_time' => Carbon::parse('2023-05-05 16:00:00'),
                'location' => 'ICS Auditorium',
                'location_details' => 'Main Campus, Building A',
                'status' => 'completed',
                'notes' => 'Open to all students',
                'created_by' => $admin->id,
                'image_path' => 'img/events/jamming.jpg',
            ],
            [
                'title' => 'Macromolecular Structure Prediction to Guide Studies of RNA Virus Proteins',
                'description' => 'A technical lecture on the latest advancements in macromolecular structure prediction and its applications in studying RNA virus proteins. This event will feature guest speakers from the field of bioinformatics.',
                'event_type' => 'Academic Lecture',
                'start_date_time' => Carbon::parse('2023-05-05 10:00:00'),
                'end_date_time' => Carbon::parse('2023-05-05 12:00:00'),
                'location' => 'Science Building, Room 105',
                'location_details' => 'East Campus',
                'status' => 'completed',
                'notes' => 'Recommended for senior students and faculty',
                'created_by' => $admin->id,
                'image_path' => 'img/events/molecular.jpg',
            ],
            [
                'title' => 'Exploring Rare-Earth-free Permanent Magnets for Electric Motors',
                'description' => 'An engineering seminar discussing sustainable alternatives to rare-earth magnets in electric motor applications. The event will cover recent research findings and potential industry applications.',
                'event_type' => 'Engineering Seminar',
                'start_date_time' => Carbon::parse('2023-05-05 13:30:00'),
                'end_date_time' => Carbon::parse('2023-05-05 15:30:00'),
                'location' => 'Engineering Building, Conference Room',
                'location_details' => 'West Campus',
                'status' => 'completed',
                'notes' => 'Registration required',
                'created_by' => $admin->id,
                'image_path' => 'img/events/magnets.jpg',
            ],
            [
                'title' => 'Seeping Through, Shaping Truth: Contesting the Politics of Language, Identity, and Representation',
                'description' => 'A humanities symposium exploring the intersection of language, identity politics, and representation in modern media. This interdisciplinary event brings together scholars from linguistics, media studies, and sociology.',
                'event_type' => 'Humanities Symposium',
                'start_date_time' => Carbon::parse('2023-05-05 09:00:00'),
                'end_date_time' => Carbon::parse('2023-05-05 15:30:00'),
                'location' => 'Liberal Arts Building, Auditorium',
                'location_details' => 'North Campus',
                'status' => 'completed',
                'notes' => 'Open to all departments',
                'created_by' => $admin->id,
                'image_path' => 'img/events/language.jpg',
            ],
            [
                'title' => 'Tatak Kyusi: Weaving Tourism, Local Enterprise and Artisans Fair',
                'description' => 'A week-long fair showcasing local artisans, entrepreneurs, and tourism initiatives from the Kyusi region. The event features workshops, exhibits, and networking opportunities for students interested in local enterprise.',
                'event_type' => 'Community Fair',
                'start_date_time' => Carbon::parse('2023-05-06 08:00:00'),
                'end_date_time' => Carbon::parse('2023-05-09 17:00:00'),
                'location' => 'University Grounds',
                'location_details' => 'Main Quadrangle',
                'status' => 'completed',
                'notes' => 'Community event open to the public',
                'created_by' => $admin->id,
                'image_path' => 'img/events/tatak.jpg',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
