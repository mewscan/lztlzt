1. Заходим в файл config.php ищем 2 строку, меняем токен "7118226465:AAHpo0QCHTxemMZRYNw6SXwTCJfj_cxvxIA" на ваш
2. Заходим в файл connect.php, меняем данные от базы данных
3. Закидываем файлы на хост, импортим файл "book.sql" в phpmyadmin и последнее что требуется - создать вебхук
4. Создаем вебхук
https://api.telegram.org/botТокенБота/setwebhook?url=путь до файла bot.php 
example - ( https://api.telegram.org/6709715648:AAEirD3Par-zyhCVJw_Nf5i_485z_Az4Xa8/setwebhook?url=domain.com/bot.php ) 