# Gamenium-GetActu
🕹️App Symfony, API de connexion avec Youtube
# Set-up
Au sein de [pubsubhubbub](https://pubsubhubbub.appspot.com/subscribe), il faut rensigner le Topic URL comme ceçi : 
```https://www.youtube.com/xml/feeds/videos.xml?channel_id=!CHANNELIDHERE!```
le Callback URL est l'url du serveur symfony puis /webhook/youtube
**ATTENTION** le websub de pubsubhubbub as une durée de vie de 5 jours.
Au sein de symfony, il faut faire un  ```composer install``` faire le .env et renseigner la clée de l'api youtube dans le .env ```YOUTUBE_API_TOKEN=!YOURAPIKEYHERE!```
