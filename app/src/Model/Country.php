<?php

namespace App\Model;

// Класс-сущность, описывающая одну страну мира
class Country
{
    public string $shortName;    // Короткое наименование страны (например, "Россия")
    public string $fullName;     // Полное наименование страны (например, "Российская Федерация")
    public string $isoAlpha2;    // Двухбуквенный код страны по ISO 3166-1 (например, "RU")
    public string $isoAlpha3;    // Трехбуквенный код страны по ISO 3166-1 (например, "RUS")
    public string $isoNumeric;   // Числовой код страны (строка, например, "643")
    public int $population;      // Население страны (целое число человек)
    public float $square;        // Площадь страны в квадратных километрах (например, 17125191.0)

    // Конструктор класса, принимает все необходимые параметры для создания объекта Country
    public function __construct(
        string $shortName,
        string $fullName,
        string $isoAlpha2,
        string $isoAlpha3,
        string $isoNumeric,
        int $population,
        float $square
    ) {
        // Присваиваем значения полям класса
        $this->shortName = $shortName;
        $this->fullName = $fullName;
        $this->isoAlpha2 = $isoAlpha2;
        $this->isoAlpha3 = $isoAlpha3;
        $this->isoNumeric = $isoNumeric;
        $this->population = $population;
        $this->square = $square;
    }
}
