#!/bin/bash

PROJECT_PATH="../" # 替换为您的项目路径
QUEUE_NAME="default" # 替换为您要监听的队列名称
LOG_FILE="$PROJECT_PATH/storage/logs/queue_$QUEUE_NAME.log"

# 检查是否已有同名队列在运行
if pgrep -f "php artisan queue:work --queue=$QUEUE_NAME" > /dev/null; then
  echo "队列 $QUEUE_NAME 已经在运行。"
  exit 1
fi

echo "启动队列 $QUEUE_NAME..."

nohup php "$PROJECT_PATH/artisan" queue:work --queue="$QUEUE_NAME" --sleep=3 --tries=3 --timeout=0 > "$LOG_FILE" 2>&1 &

echo "队列 $QUEUE_NAME 已在后台启动，日志保存在 $LOG_FILE"
