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
`3.` Run `cp .env.example .env` <br>
`4.` Run `composer install` <br>
`5.` Run `alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'` <br>
`6.` Run `sail up -d` <br>

#### <u>Database Migration and Seeder</u>
`1.` Update your .env file <br>
`2.` Run `sail artisan migrate` <br>
`3.` Run `sail artisan db:seed --class=SourcesTableSeeder` <br>

#### <u>Run the Schedular Command to Fetch News</u>
`1.` Run `sail artisan fetch:news` <br>

#### <u>For Running Automated Tests</u>
`1.` Run `sail artisan migrate --env=testing` <br>
`2.` Run `sail artisan test` <br>

#### <u>Documentation</u>
 Visit the Link `https://documenter.getpostman.com/view/15424984/2sAXxTapeB`