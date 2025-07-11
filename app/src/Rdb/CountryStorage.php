<?php

namespace App\Rdb;

use App\Model\Country;
use App\Model\CountryRepository;
use App\Model\Exceptions\CountryAlreadyExistsException;
use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\InvalidCountryDataException;

// Класс-реализация репозитория (хранилища) стран через MySQL (через mysqli)
class CountryStorage implements CountryRepository
{
    // Помощник для работы с подключением к базе данных
    private SqlHelper $sqlHelper;

    // Внедрение зависимости (SqlHelper) через конструктор
    public function __construct(SqlHelper $sqlHelper)
    {
        $this->sqlHelper = $sqlHelper;
    }

    // Получить все страны из БД
    public function getAll(): array
    {
        $countries = [];
        // Открываем соединение с БД
        $mysqli = $this->sqlHelper->openDbConnection();

        // Выполняем SQL-запрос для получения всех строк
        $result = $mysqli->query("SELECT * FROM countries");
        // Перебираем все строки результата и создаём объекты Country
        while ($row = $result->fetch_assoc()) {
            $countries[] = new Country(
                $row['short_name'],
                $row['full_name'],
                $row['iso_alpha2'],
                $row['iso_alpha3'],
                $row['iso_numeric'],
                (int)$row['population'],
                (float)$row['square']
            );
        }
        // Закрываем соединение с БД
        $mysqli->close();
        return $countries;
    }

    // Получить страну по двухбуквенному коду
    public function getByAlpha2(string $alpha2): ?Country
    {
        return $this->getByField('iso_alpha2', $alpha2);
    }

    // Получить страну по трёхбуквенному коду
    public function getByAlpha3(string $alpha3): ?Country
    {
        return $this->getByField('iso_alpha3', $alpha3);
    }

    // Получить страну по числовому коду
    public function getByNumeric(string $numeric): ?Country
    {
        return $this->getByField('iso_numeric', $numeric);
    }

    // Найти страну по короткому имени
    public function findByShortName(string $shortName): ?Country
    {
        return $this->getByField('short_name', $shortName);
    }

    // Найти страну по полному имени
    public function findByFullName(string $fullName): ?Country
    {
        return $this->getByField('full_name', $fullName);
    }

    // Сохранить новую страну в БД
    public function store(Country $country): void
    {
        $mysqli = $this->sqlHelper->openDbConnection();

        // Используем подготовленный SQL-запрос для вставки
        $stmt = $mysqli->prepare(
            "INSERT INTO countries 
                (short_name, full_name, iso_alpha2, iso_alpha3, iso_numeric, population, square)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssssid",
            $country->shortName,
            $country->fullName,
            $country->isoAlpha2,
            $country->isoAlpha3,
            $country->isoNumeric,
            $country->population,
            $country->square
        );
        // Пытаемся выполнить запрос
        if (!$stmt->execute()) {
            // Если ошибка уникальности (1062) — выбрасываем специальное исключение
            if ($mysqli->errno === 1062) {
                throw new CountryAlreadyExistsException("Duplicate code or name.");
            }
            // Другие ошибки — выбрасываем общее исключение
            throw new \Exception("DB error: " . $mysqli->error);
        }
        $stmt->close();
        $mysqli->close();
    }

    // Редактировать страну по коду (код может быть любого типа)
    public function edit(string $code, Country $country): void
    {
        // Определяем, по какому полю искать страну по формату кода
        if (preg_match('/^[A-Z]{2}$/', $code)) {
            $field = 'iso_alpha2';
        } elseif (preg_match('/^[A-Z]{3}$/', $code)) {
            $field = 'iso_alpha3';
        } elseif (preg_match('/^\d{3}$/', $code)) {
            $field = 'iso_numeric';
        } else {
            throw new InvalidCountryDataException("Invalid code format for update.");
        }

        $mysqli = $this->sqlHelper->openDbConnection();

        // Подготовленный SQL-запрос на обновление (коды не изменяются)
        $stmt = $mysqli->prepare(
            "UPDATE countries SET short_name=?, full_name=?, population=?, square=?
             WHERE $field=?"
        );
        $stmt->bind_param(
            "ssdis",
            $country->shortName,
            $country->fullName,
            $country->population,
            $country->square,
            $code
        );
        $stmt->execute();

        // Если ни одна строка не была обновлена — страны нет
        if ($stmt->affected_rows === 0) {
            throw new CountryNotFoundException("Country not found for update.");
        }
        // Если дублируется имя — исключение
        if ($mysqli->errno === 1062) {
            throw new CountryAlreadyExistsException("Duplicate name for update.");
        }

        $stmt->close();
        $mysqli->close();
    }

    // Удалить страну по коду
    public function delete(string $code): void
    {
        // Определяем поле для удаления по формату кода
        if (preg_match('/^[A-Z]{2}$/', $code)) {
            $field = 'iso_alpha2';
        } elseif (preg_match('/^[A-Z]{3}$/', $code)) {
            $field = 'iso_alpha3';
        } elseif (preg_match('/^\d{3}$/', $code)) {
            $field = 'iso_numeric';
        } else {
            throw new InvalidCountryDataException("Invalid code format for delete.");
        }

        $mysqli = $this->sqlHelper->openDbConnection();

        // Подготовленный SQL-запрос на удаление
        $stmt = $mysqli->prepare("DELETE FROM countries WHERE $field=?");
        $stmt->bind_param("s", $code);
        $stmt->execute();

        // Если страна не найдена (нет удалённых строк) — выбрасываем исключение
        if ($stmt->affected_rows === 0) {
            throw new CountryNotFoundException("Country not found for delete.");
        }

        $stmt->close();
        $mysqli->close();
    }

    // Вспомогательный приватный метод для получения страны по произвольному полю (универсальный метод для поиска)
    private function getByField(string $field, string $value): ?Country
    {
        $mysqli = $this->sqlHelper->openDbConnection();
        $sql = "SELECT * FROM countries WHERE $field = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $mysqli->close();

        if ($row) {
            // Если страна найдена — создаём и возвращаем объект Country
            return new Country(
                $row['short_name'],
                $row['full_name'],
                $row['iso_alpha2'],
                $row['iso_alpha3'],
                $row['iso_numeric'],
                (int)$row['population'],
                (float)$row['square']
            );
        }
        // Если страна не найдена — возвращаем null
        return null;
    }
}
