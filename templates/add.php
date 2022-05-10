<div class="content">
    <section class="content__side">
        <h2 class="content__side-heading">Проекты</h2>

            <nav class="main-navigation">
                    <ul class="main-navigation__list">
                        <?php foreach ($projects as $project): ?>
                        <li class="main-navigation__list-item">
                            <a class="main-navigation__list-item-link" href="index.php?id=<?= $project['id']; ?>"><?= htmlspecialchars($project['name']); ?></a>
                            <span class="main-navigation__list-item-count"><?= $project['task_count']; ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
            </nav>

                <a class="button button--transparent button--plus content__side-button"
                   href="add.php" target="project_add">Добавить проект</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Добавление задачи</h2>

        <form class="form"  action="add.php" method="post" autocomplete="off" enctype="multipart/form-data">
          <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>
            <?php $classname = isset($errors['name']) ? "form__input--error" : ""; ?>

            <input class="form__input <?= $classname; ?>" type="text" name="name" id="name" value="<?= get_post_val('name'); ?>" placeholder="Введите название">
            <?php if (isset($errors['name'])): ?><p class="form__message"><?= $errors['name']; ?></p><?php endif; ?>
          </div>

          <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>
            <?php $classname = isset($errors['project_id']) ? "form__input--error" : ""; ?>
            
            <select class="form__input form__input--select <?= $classname; ?>" name="project_id" id="project">
              <option value="">Выберите категорию</option>
              <?php foreach ($projects as $project): ?>
                  <option value="<?= $project['id']; ?>" <?php  if ($project['id'] == get_post_val('project_id')): ?>selected<?php endif; ?>><?= $project['name']; ?></option>
              <?php endforeach; ?>

              <?php if (isset($errors['project_id'])): ?><p class="form__message"><?= $errors['project_id']; ?></p><?php endif; ?>
            </select>
          </div>

          <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>
            <?php $classname = isset($errors['deadline_at']) ? "form__input--error" : ""; ?>

            <input class="form__input form__input--date <?= $classname; ?>" type="text" name="deadline_at" id="date" value="<?= get_post_val('deadline_at'); ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <?php if (isset($errors['deadline_at'])): ?><p class="form__message"><?= $errors['deadline_at']; ?></p><?php endif; ?>
          </div>

          <div class="form__row">
            <label class="form__label" for="file">Файл</label>

            <div class="form__input-file">
              <input class="visually-hidden" type="file" name="file" id="file" value="<?= get_post_val('file'); ?>">

              <label class="button button--transparent" for="file">
                <span>Выберите файл</span>
              </label>
            </div>
          </div>
          
          <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
          </div>
        </form>
    </main>
</div>
