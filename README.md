# MyOpenMuWeb

![2](https://i.imgur.com/sz3odHC.png)

MyOpenMuWeb is an open source content management system (CMS) for the [MUnique/OpenMU](https://github.com/MUnique/OpenMU) server based on PostgreSQL.

### Current status of the project
> This project is currently **under development**. You can try the current state using the available git image.

<details>
<summary>What is ready for today! (click to expand)</summary>
  
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
</details>

### Quick setup
Open the `App\Json\Config\cdb.json` file and configure a connection to the `PostgreSQL` database.

### API modification example
Go to the `src\Web\Admin Panel\API` directory and replace the old method with the new one below:
```C#
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
<details>
<summary>Adaptation of the template for a desktop computer (click to expand)</summary>

![Adaptation of the template for a desktop computer](https://i.imgur.com/EYHAUnm.png)
![Adaptation of the template for a desktop computer](https://i.imgur.com/hIrQOvz.jpg)

</details>
<details>
<summary>Adaptation of the template for smartphones (click to expand)</summary>
  
![Adaptation of the template for smartphones](https://i.imgur.com/HjOQtzM.jpg)
</details>

### For coffee :coffee:
Thanks to the developer :relaxed:

:credit_card: [donationalerts.com/r/bogdasar](https://www.donationalerts.com/r/bogdasar)

:small_blue_diamond: Toncoin: UQCAP5ywtqtW0Vz5PX9hhsZeHhJ0XN00FiP3qBs92KlW05oq

:dollar: USDT: TH6QEamrEcQArqfpWUV3PPz6TVXpNnSvbv

:moneybag: BTC: 1NzHox3KdeHVtgXaQQWpqi1WJQ29Mf81eM

[^1]: Number of lines in the request, *set by user*.
[^2]: Only pages that use database queries. Cache lifetime *set by user* for each page.
