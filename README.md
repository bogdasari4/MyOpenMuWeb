# MyOpenMuWeb
![2](https://i.imgur.com/sz3odHC.png)

MyOpenMuWeb is an open source content management system (CMS) for the [MUnique/OpenMU](https://github.com/MUnique/OpenMU) server based on PostgreSQL.
## Current status of the project
> This project is currently **under development**. You can try the current state using the available git image. Для получения дополнительной информации посетите канал [MyOpenMuWeb в Telegram](https://t.me/myopenmuweb)

Что уже готово на сегодняшний день:
- [ ] Страницы сайта
  - [x] Регистрация
    - [ ] Верификация аккаунта
  - [x] Авторизация
    - [ ] Напомнить пароль 
  - [ ] Забыл пароль
  - [x] Личный кабинет
    - [x] Информация
    - [x] Сменить пароль
    - [x] Ресет
    - [x] Распределить статы
    - [x] Телепорт
  - [x] Рейтинг
    - [x] Персонажи (топ %) [^1]
    - [x] Гильдии (топ %) [^1]
  - [x] О персонаже
  - [x] О гильдии
  - [x] Загрузки
  - [x] О сервере
- [x] Движок
  - [x] Смена языка
    - [x] Русский
    - [x] English (Google Translate)
  - [x] Кеширование страниц [^2] 

### Requirements
- Apache mod_rewrite
- PHP 8.2 or higher
  - Extensions: *intl*, *pdo_pgsql*, *pgsql*, *zip(использует composer)*, *gd*
  - Modules: *json*, *session*, *cookie*, *libxml(used for parsing RSS news feeds)*
  - [Composer](https://getcomposer.org/)
    - [Symfony](https://symfony.com/)
      - [Uid/Uuid](https://symfony.com/doc/current/components/uid.html)
      - [Cache](https://symfony.com/doc/current/cache.html)
      - [Twig](https://twig.symfony.com/)
        - intl-extra
        - extra-bundle
- [HTML5](https://html.spec.whatwg.org/multipage/) (*Basic OpenMu template*)
  - [Bootstrap](https://getbootstrap.com/)

### API modification example
Go to the src\Web\Admin Panel\API directory and replace the old method with the new one below:
```
[HttpGet]
[Route("status")]
public IActionResult ServerState()
{
    var result = new Object[_gameServers.Values.Count];

    _gameServers.Values.ForEach(async item =>
    {
        var server = item as GameServer;
        if (server is not null)
        {

            var list = new List<string>();
            await server.Context.ForEachPlayerAsync(player =>
            {
                list.Add(player.GetName());
                return Task.CompletedTask;
            }).ConfigureAwait(false);

            result[server.Id] = new {
                status = server.ServerState > 0 ? true : false,
                currentConnections = server.MaximumConnections,
                playerCount = server.Context.PlayerCount,
                playersList = list
            };

        };
    });

    return Ok(JsonSerializer.Serialize(result));
}
```

### Screenshots
> Adaptation of the template for a desktop computer (click to enlarge)

![Adaptation of the template for a desktop computer](https://i.imgur.com/EYHAUnm.png)
![Adaptation of the template for a desktop computer](https://i.imgur.com/hIrQOvz.jpg)
> Adaptation of the template for smartphones (click to enlarge)

![Adaptation of the template for smartphones](https://i.imgur.com/HjOQtzM.jpg)

### For coffee :coffee:
Thanks to the developer :relaxed:

:small_blue_diamond: Toncoin: UQCAP5ywtqtW0Vz5PX9hhsZeHhJ0XN00FiP3qBs92KlW05oq

:dollar: USDT: TH6QEamrEcQArqfpWUV3PPz6TVXpNnSvbv

:moneybag: BTC: 1NzHox3KdeHVtgXaQQWpqi1WJQ29Mf81eM

[^1]: Number of lines in the request, *set by user*.
[^2]: Only pages that use database queries. Cache lifetime *set by user* for each page.
