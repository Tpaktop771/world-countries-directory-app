-- Создание схемы и таблицы countries
DROP TABLE IF EXISTS countries;

CREATE TABLE countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    short_name VARCHAR(100) NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    iso_alpha2 CHAR(2) NOT NULL UNIQUE,
    iso_alpha3 CHAR(3) NOT NULL UNIQUE,
    iso_numeric CHAR(3) NOT NULL UNIQUE,
    population BIGINT UNSIGNED NOT NULL,
    square FLOAT UNSIGNED NOT NULL
);

-- Индексы для поиска по кодам
CREATE UNIQUE INDEX idx_countries_alpha2 ON countries (iso_alpha2);
CREATE UNIQUE INDEX idx_countries_alpha3 ON countries (iso_alpha3);
CREATE UNIQUE INDEX idx_countries_numeric ON countries (iso_numeric);
