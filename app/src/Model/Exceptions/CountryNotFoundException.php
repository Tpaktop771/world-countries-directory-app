<?php

namespace App\Model\Exceptions;

// Исключение: страна не найдена по заданному коду или имени.
// Используется для обработки ситуации, когда страна отсутствует в базе данных.
class CountryNotFoundException extends \Exception
{
    // Свойство для хранения значения, по которому велся поиск (например, код страны или имя)
    protected $searchKey;

    // Конструктор исключения
    // $message — сообщение об ошибке (по умолчанию "Country not found")
    // $searchKey — значение кода или имени, по которому выполнялся поиск
    // $code — код ошибки (обычно не используется)
    // $previous — вложенное исключение (обычно не используется)
    public function __construct($message = "Country not found", $searchKey = null, $code = 0, \Throwable $previous = null)
    {
        $this->searchKey = $searchKey; // Сохраняем ключ поиска (код или имя)
        parent::__construct($message, $code, $previous); // Вызываем конструктор родителя (Exception)
    }

    // Вернуть значение кода или имени, по которому был неудачный поиск страны.
    public function getSearchKey()
    {
        return $this->searchKey;
    }
}
