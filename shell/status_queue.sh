#!/bin/bash

QUEUE_NAME="default" # 替换为您要查看的队列名称

# 查找进程并显示信息
if pgrep -f "php artisan queue:work --queue=$QUEUE_NAME"; then
    echo "队列 $QUEUE_NAME 正在运行。"
    ps aux | grep "php artisan queue:work --queue=$QUEUE_NAME"
else
    echo "队列 $QUEUE_NAME 未运行。"
fi
