# MyOpenMuWeb
![2](https://i.imgur.com/sz3odHC.png)

[![Discord](https://img.shields.io/discord/1193794951071399956?style=for-the-badge&logo=discord&label=MyOpenMuWeb)](https://discord.gg/h4xBgxtNHw)

MyOpenMuWeb is an open source content management system (CMS) for the [MUnique/OpenMU](https://github.com/MUnique/OpenMU) server based on PostgreSQL.
## Current status of the project
> This project is currently **under development**. You can try the current state using the available git image.

What is ready for today:
- [ ] Site pages
  - [x] SignUp
    - [ ] Account verification
  - [x] SigIn
    - [ ] Forgot your password
  - [x] Personal Area
    - [x] Information
    - [x] Change password
    - [x] Rests
    - [x] Add stats
    - [x] Teleport
  - [x] Rankings
    - [x] Characters (top %) [^1]
    - [x] Guilds (top %) [^1]
  - [x] About the character
  - [x] About the guild
  - [x] Downloads
  - [x] About
- [x] Engine
  - [x] Switch language
    - [x] Russian
    - [x] English (Google Translate)
  - [x] Page caching [^2] 

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
Go to the `src\Web\Admin Panel\API` directory and replace the old method with the new one below:
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
                maximumConnections = server.MaximumConnections,
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

:credit_card: [donationalerts.com/r/bogdasar](https://www.donationalerts.com/r/bogdasar)

:small_blue_diamond: Toncoin: UQCAP5ywtqtW0Vz5PX9hhsZeHhJ0XN00FiP3qBs92KlW05oq

:dollar: USDT: TH6QEamrEcQArqfpWUV3PPz6TVXpNnSvbv

:moneybag: BTC: 1NzHox3KdeHVtgXaQQWpqi1WJQ29Mf81eM

[^1]: Number of lines in the request, *set by user*.
[^2]: Only pages that use database queries. Cache lifetime *set by user* for each page.
