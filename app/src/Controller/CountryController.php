<?php

namespace App\Controller;

// Импортируем необходимые классы
use App\Model\CountryScenarios;
use App\Model\Country;
use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\InvalidCountryDataException;
use App\Model\Exceptions\CountryAlreadyExistsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

// Контроллер, отвечающий за API для стран мира.
// Базовый маршрут для всех методов: /api/country
#[Route('/api/country')]
class CountryController extends AbstractController
{
    // Сервис бизнес-логики, инъецируется через конструктор
    private CountryScenarios $countryScenarios;

    // Инъекция зависимости (CountryScenarios)
    public function __construct(CountryScenarios $countryScenarios)
    {
        $this->countryScenarios = $countryScenarios;
    }

    // Получить список всех стран (только короткая информация + ссылка)
    // GET /api/country
    #[Route('', name: 'get_all_countries', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        // Получаем массив всех стран (объекты Country)
        $countries = $this->countryScenarios->getAll();
        $result = [];

        // Для каждой страны формируем "краткую карточку" + ссылку на подробный просмотр
        foreach ($countries as $country) {
            $result[] = [
                'shortName' => $country->shortName,      // Короткое наименование страны
                'isoAlpha2' => $country->isoAlpha2,      // Двухбуквенный код страны
                // Прямая ссылка на детальный просмотр этой страны через API
                'url' => $request->getSchemeAndHttpHost() . '/api/country/' . $country->isoAlpha2
            ];
        }

        // Отправляем клиенту массив стран в формате JSON
        return $this->json($result);
    }

    // Получить полную информацию о стране по коду (2 или 3 буквы, либо 3 цифры)
    // GET /api/country/{code}
    #[Route('/{code}', name: 'get_country_by_code', methods: ['GET'])]
    public function get(string $code): JsonResponse
    {
        try {
            // Обращаемся к слою бизнес-логики, чтобы получить страну по коду
            $country = $this->countryScenarios->get($code);
            // Если страна найдена, возвращаем её подробные данные (JSON)
            return $this->json($country);
        } catch (InvalidCountryDataException $e) {
            // Код страны невалидный (например, формат не подходит) — ошибка 400 Bad Request
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (CountryNotFoundException $e) {
            // Страна по коду не найдена — ошибка 404 Not Found
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    // Добавить новую страну
    // POST /api/country
    #[Route('', name: 'add_country', methods: ['POST'])]
    public function addCountry(Request $request): JsonResponse
    {
        try {
            // Пробуем получить JSON-данные из запроса
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                // Если пришёл некорректный JSON — возвращаем ошибку 400
                return $this->json(['error' => 'Invalid JSON'], 400);
            }
            // Создаём новый объект Country из полученных данных
            $country = new Country(
                $data['shortName'] ?? '',
                $data['fullName'] ?? '',
                $data['isoAlpha2'] ?? '',
                $data['isoAlpha3'] ?? '',
                $data['isoNumeric'] ?? '',
                (int)($data['population'] ?? 0),
                (float)($data['square'] ?? 0)
            );
            // Пытаемся сохранить страну через сценарий (он делает валидацию)
            $this->countryScenarios->store($country);
            // Если всё прошло успешно — возвращаем 204 No Content (успех без тела)
            return new JsonResponse(null, 204);
        } catch (InvalidCountryDataException $e) {
            // Ошибка валидации (неправильные данные, пустые поля и т.п.) — 400
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (CountryAlreadyExistsException $e) {
            // Нарушение уникальности (дубликат кода или названия) — 409
            return $this->json(['error' => $e->getMessage()], 409);
        }
    }

    // Редактировать данные о стране по коду
    // PATCH /api/country/{code}
    #[Route('/{code}', name: 'edit_country', methods: ['PATCH'])]
    public function edit(string $code, Request $request): JsonResponse
    {
        try {
            // Получаем обновлённые данные страны из запроса
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                // Некорректный JSON — ошибка 400
                return $this->json(['error' => 'Invalid JSON'], 400);
            }
            // Получаем оригинальные данные страны (по коду)
            $original = $this->countryScenarios->get($code);
            // Создаём новый объект Country: коды НЕ меняем, только остальные поля
            $country = new Country(
                $data['shortName'] ?? $original->shortName,
                $data['fullName'] ?? $original->fullName,
                $original->isoAlpha2,
                $original->isoAlpha3,
                $original->isoNumeric,
                isset($data['population']) ? (int)$data['population'] : $original->population,
                isset($data['square']) ? (float)$data['square'] : $original->square
            );
            // Обновляем страну в БД
            $this->countryScenarios->edit($code, $country);
            // Возвращаем новые данные страны (JSON, код 200)
            return $this->json($country, 200);
        } catch (InvalidCountryDataException $e) {
            // Ошибка валидации — 400
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (CountryAlreadyExistsException $e) {
            // Дубликат имени — 409
            return $this->json(['error' => $e->getMessage()], 409);
        } catch (CountryNotFoundException $e) {
            // Страна по коду не найдена — 404
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    // Удалить страну по коду
    // DELETE /api/country/{code}
    #[Route('/{code}', name: 'delete_country', methods: ['DELETE'])]
    public function delete(string $code): JsonResponse
    {
        try {
            // Вызываем сценарий удаления страны (валидация и удаление)
            $this->countryScenarios->delete($code);
            // Если всё хорошо — 204 No Content
            return new JsonResponse(null, 204);
        } catch (InvalidCountryDataException $e) {
            // Невалидный код страны — 400
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (CountryNotFoundException $e) {
            // Страна по коду не найдена — 404
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
