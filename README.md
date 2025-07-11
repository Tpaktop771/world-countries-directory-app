World Countries Directory API

# World Countries Directory API (Symfony + Docker)

## Описание проекта

REST API для справочника государств мира. Позволяет получить список стран, информацию о стране, а также добавить, изменить или удалить запись. Проект выполнен на базе Symfony, запускается через Docker Compose (PHP-FPM, Nginx, MySQL).

## История разработки

### 1. Инициализация
- Проект создан с помощью `symfony new app --no-git` (без autoinit git).
- Docker-окружение собрано вручную: nginx, php-fpm, mysql.
- В Dockerfile добавлены расширения pdo_mysql, mysqli, composer, symfony-cli.
- Столкнулся с ошибкой инициализации git внутри контейнера — решается флагом --no-git.

### 2. StatusController
- Генерация контроллера через symfony make:controller.
- Проверка: GET /api, GET /api/ping — оба маршрута работают (JSON-ответы).
- В случае ошибок чтение логов symfony: подсказки по недостающим пакетам composer.

### 3-4. Модель и сценарии
- Введена сущность Country (shortName, fullName, isoAlpha2, isoAlpha3, isoNumeric, population, square).
- Описан интерфейс CountryRepository и класс CountryScenarios с бизнес-логикой.
- Собственные исключения: CountryNotFoundException, CountryAlreadyExistsException, InvalidCountryDataException.
- Ошибка DI: Symfony не может внедрить интерфейс (нет реализации) — устраняется реализацией CountryStorage.

### 5-7. Инфраструктура и БД
- docker-compose: вынесены переменные окружения, добавлен сервис db (mysql:latest).
- Скрипты инициализации БД (mysql/init/1_schema.sql, 2_test_data.sql) автоматически загружаются через volume.
- Проверка через docker exec: MySQL поднялся, таблицы и тестовые страны на месте.

### 8-9. Rdb и SqlHelper
- Класс SqlHelper реализует подключение к БД (настройки из $_ENV).
- При старте выполняется pingDb для контроля доступности MySQL.
- Внедрение SqlHelper в CountryStorage для работы с базой.

### 10-13. CRUD
- Все методы CountryScenarios и CountryStorage реализованы.
- CountryController: получение списка, получение по коду, добавление, изменение, удаление.
- Валидация кодов (2/3 буквы, 3 цифры), проверка уникальности, возврат ошибок (400, 404, 409).
- При выводе списка стран: краткая информация и ссылка на подробный эндпоинт (REST/HATEOAS-стиль).
- Тестирование в Postman — все сценарии отработали, ошибки обработаны.

### 14. Документация
- Снимки Postman помещены в docs/screenshots.
- Диаграмма классов в формате PlantUML/PNG в docs.
- Подробный README.

## Структура проекта

- app/ — исходный код Symfony (контроллеры, модели, сервисы)
- mysql/init — SQL-скрипты
- nginx/ — конфиг nginx
- docs/ — документация, скриншоты, class-diagram.png
- docker-compose.yaml, Dockerfile, .env — корень

## Запуск

```sh
git clone https://github.com/your-repo/world-countries-directory-app.git
cd world-countries-directory-app
docker-compose up -d --build
```
Приложение будет доступно на http://localhost:8080

## Основные ошибки и решения

- **Не устанавливается composer пакет:** читай лог, symfony сам пишет, какой require.
- **Ошибка autowire для CountryRepository:** создай класс CountryStorage и реализуй интерфейс.
- **MySQL не стартует:** проверь volume и структуру init-скриптов.
- **Failed to open autoload_runtime.php:** composer install в контейнере после клона (docker exec -it app bash; composer install).
- **Git конфликт:** делай pull --rebase, решай конфликты вручную.

## API (примеры)

### Получить все страны
GET /api/country

Ответ:
```json
[
  {
    "shortName": "Russia",
    "isoAlpha2": "RU",
    "url": "http://localhost:8080/api/country/RU"
  },
  ...
]
```

### Получить страну по коду
GET /api/country/{code}

Ответ:
```json
{
  "shortName": "Russia",
  "fullName": "Russian Federation",
  "isoAlpha2": "RU",
  "isoAlpha3": "RUS",
  "isoNumeric": "643",
  "population": 146150789,
  "square": 17125200
}
```

### Добавить страну
POST /api/country
```json
{
  "shortName": "Canada",
  "fullName": "Canada",
  "isoAlpha2": "CA",
  "isoAlpha3": "CAN",
  "isoNumeric": "124",
  "population": 37590000,
  "square": 9984670
}
```

## Классы

- **Country:** модель-сущность
- **CountryScenarios:** бизнес-логика
- **CountryRepository:** интерфейс
- **CountryStorage:** реализация с SQL (mysqli)
- **SqlHelper:** соединение с БД
- **CountryController:** контроллер CRUD
- **StatusController:** ping/status
- **docs/class-diagram.png:** диаграмма классов (PlantUML)
