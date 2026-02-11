# devops-test-repo
Этот проект представляет собой тестовое задание для оценки навыков в области девопс разработки. Задача состоит в настройке и автоматизации инфраструктуры для простого веб-приложения, а также в создании процессов CI/CD для его развертывания и управления.

# Тестовое задание для DevOps специалиста

## Описание проекта

Это простой двухстраничный сайт на PHP для загрузки и поиска постов. Приложение состоит из следующих компонентов:
- **Страница загрузки и поиска постов:** позволяет пользователям загружать новые посты, которые сохраняются в базе данных, и осуществлять поиск   
  ![image](https://github.com/GuloGit/devops-test-repo/assets/43271316/bf8d81ba-77ef-49e4-ba38-467c8402ad4a)   

- **Страница результата поиска:** отображения ранее загруженных постов, соответствующих результату поиска   
  ![image](https://github.com/GuloGit/devops-test-repo/assets/43271316/de970f69-56b3-4856-8d89-169ccec67a29)

## Задачи

1. **Создание репозитория:**
    - Создайте новый репозиторий на GitHub (или другой платформе по вашему выбору).
    - Загрузите в репозиторий исходный код PHP проекта.

2. **Docker контейнеризация:**
    - Создайте Dockerfile для вашего PHP проекта.
    - Настройте Docker Compose файл, если необходимо, для запуска нескольких сервисов (например, PHP-FPM и Nginx).

3. **Настройка CI/CD:**
    - Выберите любую CI/CD платформу (например, GitHub Actions, GitLab CI, Jenkins и т.д.).
    - Настройте pipeline для автоматической сборки Docker-образов при каждом коммите в основной ветке репозитория.
    - Настройте этап деплоя для автоматической отправки собранных Docker-образов на удаленный сервер.

4. **Автоматическое развертывание:**
    - Настройте сервер для приема и развертывания Docker-образов.
    - Обеспечьте автоматическое развертывание обновленной версии приложения на сервере при каждом успешном билде.

5. **Документация:**
    - Напишите README файл с инструкциями по установке и запуску проекта.
    - Опишите процесс настройки CI/CD и деплоя.

## Ожидаемый результат

- Репозиторий с исходным кодом проекта и Docker конфигурациями.
- CI/CD pipeline, автоматически собирающий и деплоящий проект при каждом коммите.
- Автоматически развернутое приложение на сервере.

## Быстрый старт локально (Docker Compose)

- **Требования**:
  - Установленный Docker и Docker Compose.

- **Шаги запуска**:
  1. Клонировать репозиторий.
  2. В корне репозитория выполнить:

     ```bash
     docker-compose up --build
     ```

  3. Приложение будет доступно по адресу `http://localhost:8080`.

- **Что поднимается**:
  - `web` — контейнер с PHP-Apache и приложением.
  - `db` — контейнер MySQL с БД `test_zadanie`. Структура таблиц создаётся автоматически из SQL-скрипта в каталоге `database/`.

## Деплой в Kubernetes

- **Манифесты находятся в каталоге** `k8s/`:
  - `deployment.yaml` — Deployment приложения.
  - `service.yaml` — Service для доступа к приложению внутри кластера.
  - `ingress.yaml` — пример Ingress (домен `devops-test.local`).
  - `db-deployment.yaml`, `db-service.yaml` — пример развёртывания MySQL в кластере.
  - `configmap.yaml` — ConfigMap с настройками подключения к БД.
  - `secret-db.yaml` — Secret с учётными данными БД.

- **Базовое развёртывание** (при наличии настроенного `kubectl` и контекста кластера):

  ```bash
  kubectl apply -f k8s/db-deployment.yaml
  kubectl apply -f k8s/db-service.yaml
  kubectl apply -f k8s/configmap.yaml
  kubectl apply -f k8s/secret-db.yaml
  kubectl apply -f k8s/deployment.yaml
  kubectl apply -f k8s/service.yaml
  # при необходимости
  kubectl apply -f k8s/ingress.yaml
  ```

- **Переменные окружения приложения**:
  - В Kubernetes значения задаются через `ConfigMap` и `Secret` и автоматически подставляются в контейнер (см. `envFrom` в `deployment.yaml`).
  - В Docker Compose переменные передаются напрямую в сервис `web` и совпадают по именам: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.

## CI/CD (GitLab CI + Kubernetes)

- В репозитории присутствует файл `.gitlab-ci.yml`, который реализует:
  - **Стадию `build`**:
    - Сборка Docker-образа из корня репозитория.
    - Тегирование образа как `$CI_REGISTRY_IMAGE:$CI_COMMIT_SHA`.
    - Публикация образа в GitLab Container Registry.
  - **Стадию `deploy`**:
    - Подключение к Kubernetes-кластеру по kubeconfig.
    - Обновление образа контейнера в Deployment `devops-test-repo` командой:
      `kubectl set image deployment/devops-test-repo web=$CI_REGISTRY_IMAGE:$CI_COMMIT_SHA`.

- **Ожидаемые переменные CI/CD в GitLab**:
  - `KUBE_CONFIG` — kubeconfig, закодированный в base64, с доступом к кластеру.
  - `K8S_NAMESPACE` — (опционально) namespace, в который деплоится приложение. По умолчанию используется `default`.
  - `CI_REGISTRY`, `CI_REGISTRY_USER`, `CI_REGISTRY_PASSWORD`, `CI_REGISTRY_IMAGE` — стандартные переменные GitLab для работы с Container Registry.

- **Запуск pipeline**:
  - Pipeline запускается для веток `main`/`master`.
  - При успешном выполнении стадий `build` и `deploy` в кластере будет обновлён образ приложения.

## Деплой с помощью Helm

- **Структура чарта**:
  - Каталог `helm/devops-test-repo/` содержит:
    - `Chart.yaml` и `values.yaml`;
    - шаблоны в `templates/`: `deployment.yaml`, `service.yaml`, `ingress.yaml`, `configmap.yaml`, `secret-db.yaml`, `db-deployment.yaml`, `db-service.yaml`.
- **Основные параметры `values.yaml`**:
  - `image.repository`, `image.tag`, `image.pullPolicy` — образ приложения;
  - `replicaCount` — количество реплик приложения;
  - `service.type`, `service.port` — тип и порт Service;
  - `ingress.*` — включение/настройка Ingress;
  - `db.*` — параметры подключения приложения к БД (переменные окружения `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`);
  - `mysql.*` — параметры встроенного деплоя MySQL (можно отключить `mysql.enabled=false` и использовать внешнюю БД).
- **Примеры команд**:

  Установка/обновление релиза в namespace `default`:

  ```bash
  helm upgrade --install devops-test ./helm/devops-test-repo \
    --namespace default \
    --create-namespace
  ```

  Переопределение образа и отключение встроенной MySQL (при использовании внешней БД):

  ```bash
  helm upgrade --install devops-test ./helm/devops-test-repo \
    --namespace default \
    --set image.repository=YOUR_REGISTRY/devops-test-repo \
    --set image.tag=YOUR_TAG \
    --set mysql.enabled=false \
    --set db.host=your-external-db-host \
    --set db.user=your-user \
    --set db.password=your-password
  ```

## Примечания

- Убедитесь, что проект правильно работает после развертывания.
- Используйте лучшие практики для создания Docker-образов и настройки CI/CD.

## Дополнительные ресурсы

- [Docker Documentation](https://docs.docker.com/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [GitLab CI Documentation](https://docs.gitlab.com/ee/ci/)

