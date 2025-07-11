<?php

namespace App\Model\Exceptions;

// Исключение: некорректные данные о стране (валидация не пройдена).
// Используется, когда какое-либо поле страны не соответствует требованиям или отсутствует.
class InvalidCountryDataException extends \Exception
{
    // Имя поля, которое не прошло валидацию (например, "isoAlpha2", "population" и т.д.)
    protected $invalidField;

    // Конструктор исключения
    // $message — текст ошибки (по умолчанию "Invalid country data")
    // $invalidField — имя некорректного поля (или null, если не указано)
    // $code — числовой код ошибки (обычно не нужен)
    // $previous — вложенное исключение (редко используется)
    public function __construct($message = "Invalid country data", $invalidField = null, $code = 0, \Throwable $previous = null)
    {
        $this->invalidField = $invalidField; // Сохраняем имя некорректного поля
        parent::__construct($message, $code, $previous); // Вызываем конструктор родителя
    }

    // Вернуть имя поля, которое не прошло валидацию (если передано)
    public function getInvalidField()
    {
        return $this->invalidField;
    }
}
