<?php

namespace App\Model\Exceptions;

// Исключение: страна с таким кодом или именем уже существует.
// Используется для обработки ошибки уникальности при добавлении или изменении страны.
class CountryAlreadyExistsException extends \Exception
{
    // Свойство, в котором можно сохранить название поля, по которому найден дубликат (например, isoAlpha2 или shortName)
    protected $duplicateField;

    // Конструктор исключения
    // $message — сообщение об ошибке (по умолчанию "Country already exists")
    // $duplicateField — название поля, по которому найден дубликат (например, "shortName")
    // $code — код ошибки, как правило, не используется
    // $previous — вложенное исключение (чаще всего не используется)
    public function __construct($message = "Country already exists", $duplicateField = null, $code = 0, \Throwable $previous = null)
    {
        $this->duplicateField = $duplicateField; // Сохраняем поле дубликата
        parent::__construct($message, $code, $previous); // Вызываем конструктор родителя (Exception)
    }

    // Получить имя поля, по которому был обнаружен дубликат.
    // Например, вернёт "shortName", если дублируется краткое имя страны.
    public function getDuplicateField()
    {
        return $this->duplicateField;
    }
}
