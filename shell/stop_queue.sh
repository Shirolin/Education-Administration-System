#!/bin/bash

QUEUE_NAME="default" # 替换为您要停止的队列名称

# 查找并杀死进程
if pkill -f "php artisan queue:work --queue=$QUEUE_NAME"; then
    echo "已停止队列 $QUEUE_NAME。"
else
    echo "未找到正在运行的队列 $QUEUE_NAME。"
fi
