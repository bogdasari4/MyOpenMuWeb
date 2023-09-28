# MyOpenMuWeb
![2](https://i.imgur.com/sz3odHC.png)

MyOpenMuWeb - это система управления контентом (CMS) с открытым исходным кодом для сервера [MUnique/OpenMU](https://github.com/MUnique/OpenMU) на базе PostgreSQL.
## Текущее состояние проекта
> В настоящее время этот проект **находится в стадии разработки**. Вы можете попробовать текущее состояние, используя доступный git образ.

Что уже готово на сегодняшний день:
- [ ] Страницы сайта
  - [ ] Регистрация
    - [ ] Верификация аккаунта
  - [ ] Авторизация
    - [ ] Напомнить пароль 
  - [ ] Забыл пароль
  - [ ] Личный кабинет
  - [x] Рейтинг
    - [x] Персонажи (топ %) [^1]
    - [x] Гильдии (топ %) [^1]
  - [x] О персонаже
  - [x] О гильдии
  - [x] Загрузки
  - [x] О сервере
- [x] Движок
  - [x] Смена языка
  - [x] Кеширование страниц [^2] 

### Требования
- Apache mod_rewrite
- PHP 8.1 или выше
  - Расширения: *intl*, *pdo_pgsql*, *pgsql*, *zip(использует composer)*, *gd*
  - Модули: *json*, *session*, *cookie*
  - Composer
    - [Twig](https://twig.symfony.com/)
    - intl-extra
    - extra-bundle
- HTML (*Базовый шаблон OpenMu*)
  - [Bootstrap](https://getbootstrap.com/)

### Скриншоты
![2](https://i.imgur.com/eiMRJyT.png)

### На кофе :coffee:
Благодарность разрабу :relaxed:

:small_blue_diamond: Toncoin: UQCAP5ywtqtW0Vz5PX9hhsZeHhJ0XN00FiP3qBs92KlW05oq

:dollar: USDT: TH6QEamrEcQArqfpWUV3PPz6TVXpNnSvbv

:moneybag: BTC: 1NzHox3KdeHVtgXaQQWpqi1WJQ29Mf81eM

[^1]: Количество строк в запросе, *устанавливается пользователем*.
[^2]: Только страницы использующие запросы в базу данных. Время жизни кэша *устанавливаются пользователем* для каждой страницы.
