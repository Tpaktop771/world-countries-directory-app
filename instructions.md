# Порядок подготовки symfony-приложения на основе текущего шаблона

1. Создать папку для нового проекта с названием проекта, перенести туда файлы текущего шаблона (кроме инструкции), при необходимости изменить название docker-контейнеров в файле docker-compose.yaml. 

2. Открыть папку с проектом в VS Code.

3. В терминале в корне проекта выполнить docker-compose up -d и проверить, что контейнеры с php (где установлены composer, symfony, symfony-cli) и nginx собрались и запустились, проверить результат phpinfo() в браущере по http://localhost:8080

4. Далее надо создать symfony-проект. Для этого зайти в контейнер с php: docker exec -it app bash

5. Проверить версию composer и symfony:

composer --version

symfony version

Убедиться, что версии отображаются в CLI

6. Создать новый проект symfony:

Удалить app/index.php

Выйти из папки app: cd ..

Создать проект в папке app: symfony new app --no-git

Выйти из контейнера: exit

Поправить путь к index.php в default.conf: root /var/www/app; => root /var/www/app/public/;

Пересобрать контейнер (docker-compose down и потом docker-compose up -d --build)

7. Зайти в браузере на http://localhost:8080 и убедиться в том что проект создался и запустился
