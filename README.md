### Sameer Khan

Following are the setps to setup the project

#### <u>Environment</u>
| Tech        |  Version  |
| -----       |:---------:|
| PHP         | `^8.2`  |
| LARAVEL     | `^11.2` |

#### <u>Setup</u>

`1.` Clone Repository `https://github.com/techbysameer/news-aggregator-app.git` <br>
`2.` Run `cd news-aggregator-app` <br>
`3.` Run `cp .env.example.docker .env` <br>
`4.` Run `composer install` <br>
`5.` Run `alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'` <br>
`6.` Run `sail artisan migrate` <br>
`7.` Run `sail up -d` <br>


