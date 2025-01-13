<?php

namespace App\Http\Requests\Teacher\Course;

use App\Models\Course\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Role\Teacher;

class UpdateCourseRequest extends FormRequest
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
    }

    public function rules(): array
    {
        $courseId = $this->route('id'); // 获取路由参数 id

        return [
            'course.teacher_id' => [
                'required',
                'integer',
                Rule::exists('teachers', 'id')
            ],
            'course.teacher_nickname' => 'required|string|max:255',
            'course.name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'name')->where(function ($query) use ($courseId) {
                    return $query->where('teacher_id', $this->input('course.teacher_id'))
                                 ->where('id', '!=', $courseId);
                })
            ],
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
            'student_ids' => 'nullable|array',
            'student_ids.*' => [
                'integer',
                Rule::exists('students', 'id')
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'course.teacher_id.required' => '教师ID不能为空',
            'course.teacher_id.exists' => '教师不存在',
            'course.teacher_nickname.required' => '教师昵称不能为空',
            'course.teacher_nickname.string' => '教师昵称必须是字符串',
            'course.teacher_nickname.max' => '教师昵称不能超过255个字符',
            'course.name.required' => '课程名称不能为空',
            'course.name.string' => '课程名称必须是字符串',
            'course.name.max' => '课程名称不能超过255个字符',
            'course.name.unique' => '该教师下的课程名称已存在',
            'course.unit_fee.required' => '单元费用不能为空',
            'course.unit_fee.numeric' => '单元费用必须是数字',
            'course.unit_fee.min' => '单元费用不能小于0',
            'sub_courses.required' => '子课程不能为空',
            'sub_courses.*.year.required' => '子课程年份不能为空',
            'sub_courses.*.year.string' => '子课程年份必须是字符串',
            'sub_courses.*.month.required' => '子课程月份不能为空',
            'sub_courses.*.month.string' => '子课程月份必须是字符串',
            'sub_courses.*.month.unique' => '子课程的年月必须唯一',
            'student_ids.*.integer' => '学生ID必须是整数',
            'student_ids.*.exists' => '学生不存在',
        ];
    }
}
