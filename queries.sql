INSERT INTO users (email, name, password) VALUES 
('jane@mail.ru', 'Евгения', 'qwertyuiop'),
('dr.jerikho@mail.ru', 'Александр', '1992sql');

INSERT INTO projects (name, user_id) VALUES 
('Входящие', '2'),
('Учеба', '1'),
('Домашние дела', '2'),
('Авто', '2'),
('Работа', '1');

INSERT INTO tasks (status, name, deadline_at, user_id, project_id) VALUES
('0', 'Собеседование в IT компании', '2023.12.01', '1', '5'),
('0', 'Выполнить тестовое задание', '2023.05.01', '1', '5'),
('1', 'Сделать адание первого раздела', '2022.04.04', '1', '2'),
('0', 'Встреча с другом', '2022.05.18', '2', '1'),
('0', 'Купить корм для кота', null, '2', '3'),
('0', 'Заказать пиццу', null, '2', '3');

-- получить список из всех проектов для одного пользователя;
SELECT name FROM projects WHERE user_id = 1;

-- получить список из всех задач для одного проекта;
SELECT name FROM tasks WHERE project_id = 5;

-- пометить задачу как выполненную;
UPDATE tasks SET status = 0 WHERE id = 11;

-- обновить название задачи по её идентификатору.
UPDATE tasks SET name = 'Встреча с другом, взять подарок' WHERE id = 4;
