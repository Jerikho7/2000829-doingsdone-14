<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project): ?>
                <li class="main-navigation__list-item <?php if ($project['id'] == $project_id): ?>main-navigation__list-item--active<?php endif; ?>">
                    <a class="main-navigation__list-item-link" href="index.php?id=<?= $project['id']; ?>"><?= htmlspecialchars($project['name']); ?></a>
                    <span class="main-navigation__list-item-count"><?= $project['task_count']; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
       href="add_project.php" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
            <h2 class="content__main-heading">Список задач</h2>

            <form class="search-form" action="index.php" method="GET" autocomplete="off">
                <input class="search-form__input" type="text" name="search" value="<?= $search; ?>" placeholder="Поиск по задачам">

                <input class="search-form__submit" type="submit" name="" value="Искать">
            </form>

            <div class="tasks-controls">
                <nav class="tasks-switch">
                    <a href="index.php" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                    <a href="index.php?today=1" class="tasks-switch__item <?php  if ($today) : ?>tasks-switch__item--active<?php endif; ?>">Повестка дня</a>
                    <a href="index.php?tomorrow=1" class="tasks-switch__item <?php  if ($tomorrow) : ?>tasks-switch__item--active<?php endif; ?>">Завтра</a>
                    <a href="index.php?overdue=1" class="tasks-switch__item <?php  if ($overdue) : ?>tasks-switch__item--active<?php endif; ?>">Просроченные</a>
                </nav>

                <label class="checkbox">
                    <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?php  if ($show_complete_tasks) : ?>checked<?php endif; ?>>
                    <span class="checkbox__text">Показывать выполненные</span>
                </label>
            </div>

            <table class="tasks">
                <?php foreach ($tasks as $task): ?>
                <?php if ($task['status'] and !$show_complete_tasks): continue; endif; ?>
                <tr class="tasks__item task <?php if ($task['status']): ?>task--completed<?php endif ?> <?php if (task_deadline($task['deadline_at'])): ?>task--important<?php endif ?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="<?= $task['id']; ?>" <?php if ($task['status']): ?>checked<?php endif; ?>>
                            <span class="checkbox__text"><?= htmlspecialchars($task['name']); ?></span>
                        </label>
                    </td>
                    <td class="task__file">
                        <?php if (!empty($task['file'])): ?>
                        <a class="download-link" href="uploads/<?= $task['file']; ?>"><?= htmlspecialchars($task['file']); ?></a>
                        <?php endif; ?>
                    </td>
                    <td class="task__date"><?= $task['deadline_at']; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (count($tasks) === 0) : ?>
                    <span class=""><?= $massage; ?></span>
                <?php endif ?>
            </table>    
</main>