#!/bin/bash

DB_NAME="londonbloggers"
DB_USER="londonbloggers"

cd "$(dirname "$0")"

DB_PASS=`cat ../secrets/mysql_password`

$(mysqldump -u${DB_USER} -p${DB_PASS} --single-transaction --skip-dump-date --skip-extended-insert ${DB_NAME} > ./dump.sql)
