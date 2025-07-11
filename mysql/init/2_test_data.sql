-- Очистка таблицы
DELETE FROM countries;

-- Пример данных (названия и коды на английском!)
INSERT INTO countries 
(short_name, full_name, iso_alpha2, iso_alpha3, iso_numeric, population, square)
VALUES
('Russia', 'Russian Federation', 'RU', 'RUS', '643', 146150789, 17125191),
('United States', 'United States of America', 'US', 'USA', '840', 331893745, 9833520),
('China', 'People\'s Republic of China', 'CN', 'CHN', '156', 1444216107, 9596961),
('Germany', 'Federal Republic of Germany', 'DE', 'DEU', '276', 83166711, 357022),
('France', 'French Republic', 'FR', 'FRA', '250', 67413000, 551695),
('Brazil', 'Federative Republic of Brazil', 'BR', 'BRA', '076', 213993437, 8515767),
('Japan', 'Japan', 'JP', 'JPN', '392', 125960000, 377975),
('India', 'Republic of India', 'IN', 'IND', '356', 1393409038, 3287263);
