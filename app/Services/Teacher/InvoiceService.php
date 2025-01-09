<?php

namespace App\Services\Teacher;

use App\Models\Invoice\Invoice;
use App\Services\BaseService;

class InvoiceService extends BaseService
{
    /**
     * 分页获取账单列表
     * @param int $perPage
     * @return mixed
     */
    public function getPaginatedCourses($perPage = 10, $filters = [])
    {
        $query = Invoice::query();

        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', "{$filters['name']}%");
        }

        return $query->paginate($perPage);
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
