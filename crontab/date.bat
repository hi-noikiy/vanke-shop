@echo off 
start http://www.shop.host/crontab/cj_index.php?act=date
ping -n 5 127.1 >nul 5>nul 
taskkill /f /im IEXPLORE.exe 
exit