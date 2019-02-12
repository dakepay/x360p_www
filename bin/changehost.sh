#!/bin/bash
cmds="c1 c2 c3"
project_root_path=$(dirname $(cd $(dirname $0); pwd))
sql_bak_path=$project_root_path"/sql/bak/"
script_pid=$$

shell_name='changehost'

dbprefix='x360p_'
host=''
new_host=''
mysql_root='root'
mysql_root_pwd='root8848'
mysql_user=''
mysql_pwd=''
mysql_db=''
new_mysql_db=''
new_mysql_user=''
old_sql_bak_path=''

init_mysql_vars(){
    host=$1
    new_host=$3
	mysql_user=$1"_root"
	mysql_pwd=$2
	mysql_db=${dbprefix}$1
	new_mysql_db=${dbprefix}$3
	new_mysql_user=$3"_root"
	old_sql_bak_path=${sql_bak_path}${mysql_db}"_"$(date "+%Y%m%d")".sql"
}

check_mysql_password(){
	echo "start check mysql password ..."
	cmd="mysql -u${mysql_user} -p${mysql_pwd} -e quit"
	result=$($cmd 2>&1|grep ERROR|wc -l)
	if [ $result -ne 0 ]; then
		echo "password $mysql_pwd is wrong!"
		exit 1;
	fi
	echo "password is valid"
}

check_newdb_exists(){
	echo "start check new database exists!"
	cmd="mysql -u${mysql_root} -p${mysql_root_pwd} ${new_mysql_db} -e quit"
	result=$($cmd 2>&1|grep Unknown|wc -l)
	if [ $result -ne 1 ]; then
		echo "new database $new_mysql_db is exists"
		exit 1;
	fi
	echo "ok"
}

create_new_database(){
	echo "start create new database"
	mysql -u$mysql_root -p$mysql_root_pwd <<EOF >/dev/null 2>&1
	create database $new_mysql_db;
	grant all privileges on ${new_mysql_db}.* to '${new_mysql_user}'@'%' identified by '$mysql_pwd';
	flush privileges;
	quit;
EOF
	echo "new database create finish!"
}

export_old_data(){
	echo "start export old database"
	mysqldump -u$mysql_user -p$mysql_pwd $mysql_db > $old_sql_bak_path 2>/dev/null
	#sed -i '1d' $old_sql_bak_path >/dev/null 2>&1
	echo "database save to:${old_sql_bak_path}"
}

import_new_data(){
	echo "start import new database"
	mysql -u$new_mysql_user -p$mysql_pwd $new_mysql_db < $old_sql_bak_path >/dev/null 2>&1
	echo "database import successful!"
}

drop_old_database(){
	echo "start clean old database and user"
	mysql -u$mysql_root -p$mysql_root_pwd <<EOF >/dev/null 2>&1
	DELETE FROM mysql.user WHERE User="${mysql_user}";
	flush privileges;
	DROP DATABASE ${mysql_db};
	quit;
EOF
	echo "clearn successful!"
}

update_center_db(){
    echo "start update center database"
    mysql -u$mysql_root -p$mysql_root_pwd x360p_center <<EOF  2>&1
    update pro_client set host='$new_host' where host='$host';
    update pro_database_config set host='$new_host',\`database\`='$new_mysql_db',username='$new_mysql_user'
    where host='$host';
    quit;

EOF
    echo "center database updage successful!"
}

if [ $# != 3 ]; then
	echo  "usage: ${shell_name} <host> <password> <newhost>"
	exit 1;
fi

init_mysql_vars $1 $2 $3
check_mysql_password
check_newdb_exists
create_new_database
export_old_data
import_new_data
drop_old_database
update_center_db

echo "done!"
exit 1





