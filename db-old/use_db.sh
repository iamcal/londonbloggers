#!/bin/bash

DB_NAME="londonbloggers"
DB_USER="londonbloggers"

cd "$(dirname "$0")"

DB_PASS=`cat ../secrets/mysql_password`

mysql -u${DB_USER} -p${DB_PASS} -D${DB_NAME}
