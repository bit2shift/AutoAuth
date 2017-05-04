# AutoAuth
###### _Not to be mistaken with the browser extension._

This is a <sup><sub>_2nd_</sub></sup> revival of a project I started in early 2013.  
The primary purpose was to provide a seamless alternative to `/register` and `/login` from [AuthMe](https://dev.bukkit.org/projects/authme) ¦ [Reloaded](https://dev.bukkit.org/projects/authme-reloaded).  
Later on, I decided to implement server-side skins and capes, defined by URL in a user's forum profile.

This is a three-part project composed by:
- a Minecraft Forge coremod: ___Loader___
- a runtime-loadable jar containing the bulk functionality: ___Mod___
- a forum integration with the api used by the mod: ___Widget___

Everytime the _Loader_ is initialised, it fetches the _Mod_ `.jar` from the latest release in this repository.  
The _Loader_ is always released as "Pre-release" to distinguish it from _Mod_ releases.

A piece of history is included in [`CHANGELOG.old`](CHANGELOG.old) along with the original forum post:  
![](https://cloud.githubusercontent.com/assets/6282023/25702435/e0de5cca-30c8-11e7-9d48-9a31fb731979.png)
