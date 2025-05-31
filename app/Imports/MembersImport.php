<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class MembersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'studentnumber' => $row['student_number'],
            'firstname' => $row['first_name'],
            'lastname' => $row['last_name'],
            'middlename' => $row['middle_name'] ?? null,
            'suffix' => $row['suffix'] ?? null,
            'course' => $row['course'],
            'major' => $row['major'] ?? null,
            'year' => $row['year'],
            'section' => $row['section'],
            'mobile_no' => $row['mobile_number'],
            'email' => $row['email'],
            'password' => Hash::make('password'), // Default password
            'user_role' => 'member',
        ]);
    }
}
