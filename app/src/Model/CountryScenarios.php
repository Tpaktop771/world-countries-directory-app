<?php

namespace App\Model;

use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\InvalidCountryDataException;
use App\Model\Exceptions\CountryAlreadyExistsException;

// Класс, реализующий бизнес-логику (сценарии) работы со странами.
// Является "прослойкой" между контроллером и хранилищем (репозиторием).
class CountryScenarios
{
    // Хранилище стран (интерфейс, реализуется классом CountryStorage)
    private CountryRepository $countryRepository;

    // Инъекция зависимости через конструктор
    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    // Получить список всех стран
    public function getAll(): array
    {
        return $this->countryRepository->getAll();
    }

    // Получить страну по любому коду (двухбуквенному, трёхбуквенному или числовому)
    public function get(string $code): Country
    {
        // Определяем тип кода с помощью регулярных выражений и вызываем нужный метод репозитория
        if (preg_match('/^[A-Z]{2}$/', $code)) {
            // Двухбуквенный код
            $country = $this->countryRepository->getByAlpha2($code);
        } elseif (preg_match('/^[A-Z]{3}$/', $code)) {
            // Трёхбуквенный код
            $country = $this->countryRepository->getByAlpha3($code);
        } elseif (preg_match('/^\d{3}$/', $code)) {
            // Числовой код (строка из трёх цифр)
            $country = $this->countryRepository->getByNumeric($code);
        } else {
            // Невалидный формат кода — выбрасываем исключение
            throw new InvalidCountryDataException("Invalid country code format.");
        }
        if (!$country) {
            // Страна не найдена — выбрасываем исключение
            throw new CountryNotFoundException("Country not found.");
        }
        // Возвращаем найденную страну
        return $country;
    }

    // Добавить новую страну
    public function store(Country $country): void
    {
        // Проверяем валидность isoAlpha2 (2 большие буквы)
        if (!preg_match('/^[A-Z]{2}$/', $country->isoAlpha2)) {
            throw new InvalidCountryDataException("Invalid isoAlpha2 code.");
        }
        // Проверяем валидность isoAlpha3 (3 большие буквы)
        if (!preg_match('/^[A-Z]{3}$/', $country->isoAlpha3)) {
            throw new InvalidCountryDataException("Invalid isoAlpha3 code.");
        }
        // Проверяем валидность isoNumeric (3 цифры)
        if (!preg_match('/^\d{3}$/', $country->isoNumeric)) {
            throw new InvalidCountryDataException("Invalid isoNumeric code.");
        }
        // Проверяем, что названия не пустые
        if (empty($country->shortName) || empty($country->fullName)) {
            throw new InvalidCountryDataException("Names must be filled.");
        }
        // Проверяем, что население и площадь неотрицательные
        if ($country->population < 0 || $country->square < 0) {
            throw new InvalidCountryDataException("Population and square must be >= 0.");
        }
        // Проверяем уникальность кодов и наименований
        if ($this->countryRepository->getByAlpha2($country->isoAlpha2) ||
            $this->countryRepository->getByAlpha3($country->isoAlpha3) ||
            $this->countryRepository->getByNumeric($country->isoNumeric) ||
            $this->countryRepository->findByShortName($country->shortName) ||
            $this->countryRepository->findByFullName($country->fullName)) {
            throw new CountryAlreadyExistsException("Duplicate code or name.");
        }
        // Если все проверки пройдены — добавляем страну в хранилище
        $this->countryRepository->store($country);
    }

    // Редактировать страну по коду
    public function edit(string $code, Country $country): void
    {
        // Проверяем, что код валидный (2/3 буквы или 3 цифры)
        if (!(preg_match('/^[A-Z]{2}$/', $code) ||
            preg_match('/^[A-Z]{3}$/', $code) ||
            preg_match('/^\d{3}$/', $code))) {
            throw new InvalidCountryDataException("Invalid code format.");
        }

        // Получаем оригинальную страну для сравнения кодов
        $original = $this->get($code);

        // Запрещаем изменение кодов страны
        if (
            (!empty($country->isoAlpha2) && $country->isoAlpha2 !== $original->isoAlpha2) ||
            (!empty($country->isoAlpha3) && $country->isoAlpha3 !== $original->isoAlpha3) ||
            (!empty($country->isoNumeric) && $country->isoNumeric !== $original->isoNumeric)
        ) {
            throw new InvalidCountryDataException("Country codes can't be changed.");
        }
        // Проверяем, что названия не пустые
        if (empty($country->shortName) || empty($country->fullName)) {
            throw new InvalidCountryDataException("Names must be filled.");
        }
        // Проверяем, что население и площадь неотрицательные
        if ($country->population < 0 || $country->square < 0) {
            throw new InvalidCountryDataException("Population and square must be >= 0.");
        }
        // Проверяем уникальность короткого имени (не должно быть у других стран)
        $byShort = $this->countryRepository->findByShortName($country->shortName);
        if ($byShort && $byShort->isoAlpha2 !== $original->isoAlpha2) {
            throw new CountryAlreadyExistsException("Short name already used by another country.");
        }
        // Проверяем уникальность полного имени (не должно быть у других стран)
        $byFull = $this->countryRepository->findByFullName($country->fullName);
        if ($byFull && $byFull->isoAlpha2 !== $original->isoAlpha2) {
            throw new CountryAlreadyExistsException("Full name already used by another country.");
        }
        // Если все проверки пройдены — редактируем страну в хранилище
        $this->countryRepository->edit($code, $country);
    }

    // Удалить страну по коду
    public function delete(string $code): void
    {
        // Проверяем валидность кода
        if (!(preg_match('/^[A-Z]{2}$/', $code) ||
            preg_match('/^[A-Z]{3}$/', $code) ||
            preg_match('/^\d{3}$/', $code))) {
            throw new InvalidCountryDataException("Invalid code format.");
        }

        // Проверяем, что страна существует (иначе выбросится исключение)
        $this->get($code);

        // Удаляем страну из хранилища
        $this->countryRepository->delete($code);
    }
}
