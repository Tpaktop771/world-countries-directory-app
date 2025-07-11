<?php

namespace App\Model;

// Интерфейс хранилища стран
// Описывает методы для работы с коллекцией стран (реализуется, например, через класс CountryStorage)
interface CountryRepository
{
    // Получить массив всех стран (Country)
    public function getAll(): array;

    // Получить страну по двухбуквенному коду (например, "RU")
    public function getByAlpha2(string $alpha2): ?Country;

    // Получить страну по трёхбуквенному коду (например, "RUS")
    public function getByAlpha3(string $alpha3): ?Country;

    // Получить страну по числовому коду (например, "643")
    public function getByNumeric(string $numeric): ?Country;

    // Найти страну по короткому наименованию (например, "Russia")
    public function findByShortName(string $shortName): ?Country;

    // Найти страну по полному наименованию (например, "Russian Federation")
    public function findByFullName(string $fullName): ?Country;

    // Сохранить новую страну в хранилище
    public function store(Country $country): void;

    // Отредактировать существующую страну по коду
    // code — любой валидный код (двухбуквенный, трёхбуквенный или числовой)
    public function edit(string $code, Country $country): void;

    // Удалить страну по коду
    public function delete(string $code): void;
}
