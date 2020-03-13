#!/bin/bash

cd repo
git fetch
if [ $(git rev-parse origin/master) == $(git rev-parse HEAD) ]
then
	echo "git already up to date."
else
	hoje=$(date +'%y%m%d')
	git stash save "stash-$hoje"
	git pull origin master 
	cd public_html 
	/usr/local/bin/composer update --working-dir=/home/compartilhatube/public_html/
	result_composer=$?
	if [ $result_composer = 0 ]
	then
		echo "Composer OK. continuing. "
		file="./.htaccess"
		if [ -f "$file" ]
		then
			echo ""
		else
			echo "$file na raiz not found."
			cp ./.htaccess_prod ./.htaccess
		fi
		file="./admin/.htaccess"
		if [ -f "$file" ]
		then
			echo ""
		else
			echo "$file em admin not found."
			cp ./admin/.htaccess_prod ./admin/.htaccess
		fi
		file="./app/.htaccess"
		if [ -f "$file" ]
		then
			echo ""
		else
			echo "$file em app not found."
			cp ./app/.htaccess_prod ./app/.htaccess
		fi
		file="./services/.htaccess"
		if [ -f "$file" ]
		then
			echo ""
		else
			echo "$file em services not found."
		fi
		file="./grupo/.htaccess"
		if [ -f "$file" ]
		then
			echo ""
		else
			echo "$file em grupo not found."
			cp ./grupo/.htaccess_prod ./grupo/.htaccess
		fi
		file="./api/.htaccess"
		if [ -f "$file" ]
		then
			echo ""
		else
			echo "$file em api not found."
			cp ./api/.htaccess_prod ./api/.htaccess
		fi 
		cd app
		/usr/local/bin/phpunit --bootstrap /home/compartilhatube/public_html/app/test/autoload.php /home/compartilhatube/public_html/app/test
		result_phpunit=$?
		cd ../../../
		echo "result of phpunit:$result_phpunit"
		if [ $result_phpunit = 0 ]
		then
			echo "tests are ok, continuing."
			if [ -f "./public_html_backup" ]
			then
				echo "apagando pasta public_html_backup"
				rm -rf ./public_html_backup
			else
				echo ""
			fi
			echo "executing backup on folder public_html to public_html_backup"
			cp -ufR ./public_html ./public_html_backup
			cd repo
			echo "atualizando public_html"
			cp -ufR ./public_html ../
			echo "Deploy done! Success!"
		else
			echo "Problems found at test. Deploy will not happen!"
		fi
	else
		echo "Composer error: $result_composer"
	fi
fi
echo "END OF DEPLOY!"
