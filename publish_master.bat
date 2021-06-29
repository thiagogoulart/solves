@echo off
set /P comentario= "Digite um comentario para Commit e TAG de release: ": 
git pull
git add .
git commit . -m "publish_master: %comentario%"
git push
git checkout master
git pull
git merge development
git push origin master
:: atualiza informações e cria tag de versão
for /f %%i in ('git describe --abbrev^=0 --tags') do set ultima_tag=%%i
echo "Ultima TAG: %ultima_tag%"
set /P tag_versao= "Digite uma versao para TAG de release: ": 
git tag -a %tag_versao% -m "Release %tag_versao%: %comentario%"
git push origin %tag_versao%
git push
git checkout development
echo "Voltando ambiente local para branch development"
echo "Publicacao finalizada!"
