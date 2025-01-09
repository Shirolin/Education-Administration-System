<?php

namespace App\Services;

class CourseService extends BaseService
{
    /**
     * 获取课程列表
     * @return array
     */
    public function index()
    {
        return [
            ['id' => 1, 'name' => '课程1'],
            ['id' => 2, 'name' => '课程2'],
        ];
    }

    /**
     * 创建课程
     * @return array
     */
    public function store()
    {
        return [
            'id' => 3,
            'name' => '课程3',
        ];
    }

    /**
     * 获取单个课程信息
     * @param $id
     * @return array
     */
    public function show($id)
    {
        return [
            'id' => $id,
            'name' => '课程' . $id,
        ];
    }

    /**
     * 更新课程信息
     * @param $id
     * @return array
     */
    public function update($id)
    {
        return [
            'id' => $id,
            'name' => '课程' . $id,
        ];
    }

    /**
     * 删除课程
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        return [
            'id' => $id,
            'name' => '课程' . $id,
        ];
    }
}
