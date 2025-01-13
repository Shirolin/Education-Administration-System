<?php

namespace App\Http\Requests\Teacher\Course;

use App\Models\Course\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Role\Teacher;

class CreateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('course.teacher_id')) {
            $teacher = Teacher::find($this->input('course.teacher_id'));
            if ($teacher) {
                $this->merge([
                    'course' => array_merge($this->input('course'), [
                        'teacher_nickname' => $teacher->nickname
                    ])
                ]);
            }
        }

        $this->merge([
            'course' => array_merge($this->input('course', []), [
                'sub_courses_count' => count($this->input('sub_courses', [])),
                'status' => Course::STATUS_ENABLED
            ])
        ]);
    }

    public function rules(): array
    {
        return [
            'course.teacher_id' => [
                'required',
                'integer',
                Rule::exists('teachers', 'id')
            ],
            'course.teacher_nickname' => 'required|string|max:255',
            'course.name' => 'required|string|max:255',
            'course.unit_fee' => 'required|numeric|min:0',
            'sub_courses' => 'nullable|array',
            'sub_courses.*.year' => 'required_with:sub_courses|string',
            'sub_courses.*.month' => [
                'required_with:sub_courses',
                'string',
                function ($attribute, $value, $fail) {
                    $subCourses = $this->input('sub_courses', []);
                    $currentSubCourse = $subCourses[explode('.', $attribute)[1]];
                    foreach ($subCourses as $index => $subCourse) {
                        if (
                            $index != explode('.', $attribute)[1] &&
                            $subCourse['year'] == $currentSubCourse['year'] &&
                            $subCourse['month'] == $currentSubCourse['month']
                        ) {
                            return $fail('同个课程下子课程的年月必须唯一');
                        }
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'course.teacher_id.required' => '教师ID不能为空',
            'course.teacher_id.exists' => '教师不存在',
            'course.teacher_nickname.required' => '教师昵称不能为空',
            'course.name.required' => '课程名称不能为空',
            'course.unit_fee.required' => '单元费用不能为空',
            'sub_courses.required' => '子课程不能为空',
            'sub_courses.*.year.required' => '子课程年份不能为空',
            'sub_courses.*.month.required' => '子课程月份不能为空',
            'sub_courses.*.month.unique' => '子课程的年月必须唯一',
        ];
    }
}
