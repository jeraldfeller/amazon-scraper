#!/bin/bash
for(( c=1; c<=2000; c++))
do
	php /var/www/html/am/action/exec-rescan-success.php
done
