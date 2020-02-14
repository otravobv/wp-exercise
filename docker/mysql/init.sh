#!/usr/bin/env bash

# Stop on failure
set -e

mysql -u root -p${MYSQL_ROOT_PASSWORD} -e "CREATE DATABASE IF NOT EXISTS ${MAIN_DATABASE_NAME};" 2>/dev/null
