<?php

namespace App\Services\Student;

use App\Services\BaseService;

class MyInvoiceService extends BaseService
{
    /**
     * 获取账单列表
     * @return array
     */
    public function index()
    {
        return [
            ['id' => 1, 'name' => '账单1'],
            ['id' => 2, 'name' => '账单2'],
        ];
    }

    /**
     * 创建账单
     * @return array
     */
    public function store()
    {
        return [
            'id' => 3,
            'name' => '账单3',
        ];
    }

    /**
     * 获取单个账单信息
     * @param $id
     * @return array
     */
    public function show($id)
    {
        return [
            'id' => $id,
            'name' => '账单' . $id,
        ];
    }

    /**
     * 更新账单信息
     * @param $id
     * @return array
     */
    public function update($id)
    {
        return [
            'id' => $id,
            'name' => '账单' . $id,
        ];
    }

    /**
     * 删除账单
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        return [
            'id' => $id,
            'name' => '账单' . $id,
        ];
    }

    /**
     * 发送账单
     * @param $id
     * @return array
     */
    public function send($id)
    {
        return [
            'id' => $id,
            'name' => '账单' . $id,
        ];
    }
}
