<?php

namespace App\Rdb;

// Вспомогательный класс для подключения и проверки соединения с базой данных MySQL
class SqlHelper
{
    private string $host;      // Адрес хоста БД
    private string $user;      // Имя пользователя БД
    private string $password;  // Пароль пользователя БД
    private string $database;  // Имя базы данных
    private int $port;         // Порт подключения к БД

    // Конструктор — инициализирует параметры подключения и проверяет соединение с БД
    public function __construct()
    {
        // Чтение параметров из глобального массива окружения (или дефолтные значения)
        $this->host     = $_ENV['DB_HOST']     ?? 'db';         // по умолчанию 'db' — имя сервиса docker-compose
        $this->user     = $_ENV['DB_USERNAME'] ?? 'user';       // по умолчанию 'user'
        $this->password = $_ENV['DB_PASSWORD'] ?? 'password';   // по умолчанию 'password'
        $this->database = $_ENV['DB_NAME']     ?? 'world';      // по умолчанию 'world'
        $this->port     = (int) ($_ENV['DB_PORT'] ?? 3306);     // по умолчанию порт 3306

        // Проверяем доступность БД при создании объекта
        $this->pingDb();
    }

    // Открыть и вернуть новое соединение с MySQL (использует параметры из конструктора)
    public function openDbConnection(): \mysqli
    {
        $mysqli = new \mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->database,
            $this->port
        );

        // Если не удалось подключиться — выбрасываем исключение
        if ($mysqli->connect_error) {
            throw new \Exception('Ошибка подключения к БД: ' . $mysqli->connect_error);
        }
        return $mysqli;
    }

    // Приватный метод для проверки, что БД доступна (ping)
    private function pingDb(): void
    {
        // Открываем соединение (ошибки игнорируем с помощью @, чтобы обработать вручную)
        $mysqli = @$this->openDbConnection();
        // Если соединение не удалось или ping() возвращает false — исключение
        if (!$mysqli || !$mysqli->ping()) {
            throw new \Exception('MySQL ping failed!');
        }
        // Закрываем соединение после проверки
        $mysqli->close();
    }
}
