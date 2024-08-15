# Neiro - объединение популярных нейросетей!
## О проекте

Проект Neiro - это API сервис для работы с нейросетями.
На данный момент к Neiro подключены нейросети для работы с тектом, изображениями,
распознованием голоса и озвучкой.

Neiro разработан полностью мной и впервую очередь создавался для демонстрации моего опыта
в работе с Laravel

Данный проект включает в себя:
1. Проектирование API и написание скриптов для работы с нейросетями: GigaChat, SaluteSpeech, Fusion Brain
2. Написание интерфейсов, абстрактных классов, контроллеров и моделей
3. Написание Feature и Unit тестов
4. Работу с очередями

Проект Neiro не является коммерческим продуктом и используется лично автором!

Telegram: <a href="https://t.me/iliyalyachuk">@iliyalyachuk</a>

Почта: <a href="mailto:iliya-rabota97@mail.ru">iliya-rabota97@mail.ru</a>

## Подключение

Для работы вам необходимо создать файл .env в главной директории и прописать в него доступы 
к нейросетям, а также указать корректный домен и доступы для подключения к БД.

GigaChat API - https://developers.sber.ru/docs/ru/gigachat/api/overview

SaluteSpeech API - https://developers.sber.ru/docs/ru/salutespeech/overview

```
// ключ RQUID для отправки запросов
SBER_RQUID=""

// данные для авторизации GigaChat
GIGACHAT_AUTH_TOKEN=""
GIGACHAT_SCOPE="GIGACHAT_API_PERS"

// данные для авторизации SaluteSpeech
SALUTESPEECH_AUTH_TOKEN=""
SALUTESPEECH_SCOPE="SALUTE_SPEECH_PERS"
```


