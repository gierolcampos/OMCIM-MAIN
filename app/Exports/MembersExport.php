<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::where('user_role', 'member')
            ->select('id', 'studentnumber', 'firstname', 'lastname', 'middlename', 'suffix',
                    'course', 'major', 'year', 'section', 'mobile_no', 'email', 'created_at')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Student Number',
            'First Name',
            'Last Name',
            'Middle Name',
            'Suffix',
            'Course',
            'Major',
            'Year',
            'Section',
            'Mobile Number',
            'Email',
            'Created At'
        ];
    }
}
