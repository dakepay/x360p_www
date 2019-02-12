#!/bin/bash
#开启队列
#queue_name:
#	①AutoCreateHomework(自动创建作业相当于定时任务)
#	②SendWxTplMsg(发送微信模板消息的队列)
#	②TransferMedia(从微信公众号上传媒体文件)

#websocket 守护进程
#/usr/bin/php ./public/index.php server/web_socket/start &
shell_name="x360p"
project_root_path=$(dirname $(cd $(dirname $0); pwd))
think_php_file="${project_root_path}/think"
command_list="Base SendWxTplMsg TransferMedia"

if [ ! -f "$think_php_file" ]; then
  echo "$think_php_file not exists"
  exit
fi

#/usr/bin/php think queue:listen --queue AutoCreateHomework &
#/usr/bin/php think queue:listen --queue SendWxTplMsg &
#/usr/bin/php think queue:listen --queue TransferMedia &
script_pid=$$
pid=''
getPid(){
    process_alias=$1
    site_alias=$2
    if [ $process_alias = 'web_socket' ]
    then
        process_alias='server/web_socket/start'
    fi
    if [ -n "$site_alias" ] ; then
    pid=$(ps -aux | grep ${process_alias} | grep 'think' | grep "${site_alias}" | grep -v grep | awk ' { print $2 } ')
    else
    pid=$(ps -aux | grep ${process_alias} | grep 'think' | grep -v grep | awk ' { print $2 } ')
    fi
}

closeProcess(){
    getPid $1 $2
    if [ "$pid" ]
    then
        for var in $pid
        do
            if [ $$ = $var ]
            then
                continue
            fi
            kill -9 $var
        done
    fi
}


queue(){
    case $2 in
        'start')
            getPid $1 $3
            if [ "$pid" ]
            then
                echo "queue [$1] --site [$3] is running, do not need start"
                return
            fi
            /usr/bin/php think queue:listen --queue $1 --tries 5 --site $3 > /dev/null 2>&1 &

            if [ $1 = 'AutoCreateHomework' ]
            then
                /usr/bin/php think create_homework &
            fi
        ;;
        'stop')
            closeProcess $1 $3
            echo "process $1 --site $3 stoped"
        ;;
        'restart')
            closeProcess $1
            /usr/bin/php think queue:listen --queue $1 --site $3&
            echo "process $1 --site $3 started"
        ;;
        *)
        echo 'valid parameter $2 must in [start, stop, restart]'
        ;;
    esac
}

websocket(){
    case $2 in
        'start')
            getPid $1 $3
            if [ "$pid" ]
            then
                echo "websocket [$1] [$3] is running, do not need start"
                return
            fi
            /usr/bin/php ./public/index.php server/web_socket/start $3 > /dev/null 2>&1 &
        ;;
        'stop')
            closeProcess $1 $3
            echo "process $1 $3 stoped"
        ;;
        'restart')
            closeProcess $1 $3
            /usr/bin/php ./public/index.php server/web_socket/start $3 > /dev/null 2>&1 &
            echo "web_socket start"
        ;;
        *)
        echo 'valid parameter $2 must in [start, stop, restart]'
        ;;
    esac
}


case $1 in
    '-help')
        
        echo "./$shell_name ${command_list// /|} start|stop|restart [site_alias]"
    ;;
    'all')
        queue Base $2 $3
        queue SendWxTplMsg $2 $3
        queue TransferMedia $2 $3
    ;;
    'Base' )
        queue Base $2 $3
    ;;
    'SendWxTplMsg')
        queue SendWxTplMsg $2 $3
    ;;
    'TransferMedia')
        queue TransferMedia $2 $3
    ;;
    'web_socket')
        websocket web_socket $2 $3
    ;;
    *)
    echo 'valid parameter $1 must in [all, Base, SendWxTplMsg, TransferMedia]'
    ;;
esac
