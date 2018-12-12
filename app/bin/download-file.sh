#!/usr/bin/env bash

curl -L http://guvm.mvd.ru/upload/expired-passports/list_of_expired_passports.csv.bz2 | bzip2 -d > /tmp/list_of_expired_passports.csv
