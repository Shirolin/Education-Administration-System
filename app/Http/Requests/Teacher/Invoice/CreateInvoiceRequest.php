<?php

namespace App\Http\Requests\Teacher\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Role\Teacher;
use App\Models\Course\SubCourse;
use App\Models\Invoice\Invoice;
use App\Models\Payment\StudentPurchasedCourse;
use Illuminate\Support\Facades\Auth;

// CREATE TABLE "public"."invoices" (
//     "id" int8 NOT NULL DEFAULT nextval('invoices_id_seq'::regclass),
//     "invoice_no" varchar(20) COLLATE "pg_catalog"."default" NOT NULL,
//     "course_id" int4 NOT NULL,
//     "student_id" int4 NOT NULL,
//     "creator_id" int4 NOT NULL,
//     "total_amount" numeric(10,2) NOT NULL,
//     "currency" varchar(10) COLLATE "pg_catalog"."default" NOT NULL DEFAULT 'CNY'::character varying,
//     "status" int2 NOT NULL,
//     "created_at" timestamp(0),
//     "updated_at" timestamp(0),
//     CONSTRAINT "invoices_pkey" PRIMARY KEY ("id"),
//     CONSTRAINT "invoices_invoice_no_unique" UNIQUE ("invoice_no")
// );
// CREATE TABLE "public"."invoice_items" (
//     "id" int8 NOT NULL DEFAULT nextval('invoice_items_id_seq'::regclass),
//     "invoice_id" int4 NOT NULL,
//     "sub_course_id" int4 NOT NULL,
//     "amount" numeric(10,2) NOT NULL,
//     "created_at" timestamp(0),
//     "updated_at" timestamp(0),
//     CONSTRAINT "invoice_items_pkey" PRIMARY KEY ("id")
// );

// 请求body结构例子
// {
//     "course_id": 15,
//     "sub_course_ids": [
//         44,
//         45
//     ],
//     "student_id": 30
// }

class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $creatorId = Auth::id();
        $teacher = Teacher::find($creatorId);
        if ($teacher) {
            $this->merge([
                'creator_id' => $creatorId,
                'creator_nickname' => $teacher->nickname
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id')
            ],
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id')
            ],
            'sub_course_ids' => 'nullable|array',
            'sub_course_ids.*' => [
                'integer',
                Rule::exists('sub_courses', 'id'),
                function ($attribute, $value, $fail) {
                    $studentId = $this->input('student_id');
                    $exists = StudentPurchasedCourse::where('student_id', $studentId)
                        ->where('sub_course_id', $value)
                        ->exists();
                    if ($exists) {
                        $fail('学生已购买该子课程');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => '课程ID不能为空',
            'course_id.exists' => '课程不存在',
            'student_id.required' => '学生ID不能为空',
            'student_id.exists' => '学生不存在',
            'sub_course_ids.*.integer' => '子课程ID必须是整数',
            'sub_course_ids.*.exists' => '子课程不存在',
            'sub_course_ids.*.function' => '学生已购买该子课程',
        ];
    }

    public function getInvoiceData(): array
    {
        $totalAmount = collect($this->input('sub_course_ids', []))->reduce(function ($carry, $subCourseId) {
            $subCourse = SubCourse::find($subCourseId);
            return $carry + ($subCourse ? $subCourse->fee : 0);
        }, 0);

        return [
            'course_id' => $this->input('course_id'),
            'student_id' => $this->input('student_id'),
            'creator_id' => $this->input('creator_id'),
            'invoice_no' => Invoice::generateInvoiceNo(),
            'total_amount' => $totalAmount,
        ];
    }

    public function getInvoiceItemsData(): array
    {
        return collect($this->input('sub_course_ids', []))->map(function ($subCourseId) {
            $subCourse = SubCourse::find($subCourseId);
            return [
                'sub_course_id' => $subCourseId,
                'amount' => $subCourse ? $subCourse->fee : 0,
            ];
        })->toArray();
    }
}
