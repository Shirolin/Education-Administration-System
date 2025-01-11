#!/bin/bash

QUEUE_NAME="default" # 替换为您要重启的队列名称

echo "重启队列 $QUEUE_NAME..."

# 停止队列
if pkill -f "php artisan queue:work --queue=$QUEUE_NAME"; then
    echo "已停止队列 $QUEUE_NAME。"
else
    echo "未找到正在运行的队列 $QUEUE_NAME。"
fi

sleep 1 # 等待 1 秒，确保进程完全停止

# 启动队列
PROJECT_PATH="/path/to/your/project" # 替换为您的项目路径
LOG_FILE="$PROJECT_PATH/storage/logs/queue_$QUEUE_NAME.log"

nohup php "$PROJECT_PATH/artisan" queue:work --queue="$QUEUE_NAME" --sleep=3 --tries=3 --timeout=0 >"$LOG_FILE" 2>&1 &

echo "队列 $QUEUE_NAME 已在后台重启，日志保存在 $LOG_FILE"
