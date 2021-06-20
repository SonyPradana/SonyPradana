# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Support cache (using symfony)
- Add global some function:
  - slugify, startsWith, dd, app_path, base_path, abort, api_abort, view
- Better chain code in cron shedule
- Add command cron:list to show all cron register

### Changed
- Change namespace from Simpus\Services to Model\JadwalKia
- Change namespace from Simpus\Helper to Helper\String
- Help command now create in every command (child)
- Class rename Commands/MakerCommand.php to Commands/MakeCommand.php
- Rename file from Schadule.php to Schedule.php
- Cron schedule for maintance, now deleting oldest log and backup then in storage/cron_log

### Deprecated
 - StringManipulation.php willbe merge with Manipulation.php
 - StringSanitization.php willbe merge with Str.php
 - StringValidation.php EOL

## [2.3.8] - 2021-05-24
### Added
- Support cron schaduler command (one cron to all job schaduller)
- MyModel support costume join
- MyQuery insert class supoort multy value single function
- Medical Record add NIK and Nomor jaminan (biodata)
- Medical Record CRUD support biodata
- Add Regestration Medical record
- Router prefix (grouping route expression with same prefix)
- Covid Tracker now using async api call 4X faster reguler catch api

### Changed
- Register command now in CLI.php (class) not in command.config.php (array)
- Cek nomor terahir menggunakan id dan nomor rm tertinggi
- [Style] Menyamakan variable warna (css), merge table css ke full.style.css
- Change index router to static class

### Fixed
- Var_dump in pruduction (Insert.php)
- Fix MyModel skip value = 0 (zero), php detect 0 if null
- Fix MyModel return where statment to '()', if where statment null MyModel will not return the query
- Fix MyModel prefix ambigus column name, support costume prefix

### Removed
- MixController, mixing js or css to one single file

### Security
- decress memory use in unit test (add reset router)

## [2.3.7] - 2021-04-16
### Added
- Add page info vaksin (indonesia)
- MyQuery support static function
- Costumeable CLI command (registered command function)
- Styling CLI (color)
- Splint CLI code to some funtion
- Singleton MyPDO (database connation)

### Fixed
- Fix story url not found because space in url
- Rename robot.txt to robots.txt

### Changed
- MyQuery insert now using associative array
- Refactory CLI base code

## [2.3.6] - 2021-03-05
### Added
- Add simple cms (dashbord admin)
- Add Join table, between, multy costume where in MyQuery
- Add abstarct class for Services
- Default services respone (api)

### Fixed
- dashboard admin now can refresh anywhere (css not broken anymore)

## [2.3.5] - 2021-02-05
### Added
- Support VUE Router
- Support Tailwind-CSS (partial)
- Add admin dashbord page
- Add services for ganerate new jadwal imun
- Add Site Map generator services
- Unit test for Router

### Changed
- CLI now using bootstrap autoload
- Config location now able to change path location
- All template page now referance resource base on config location setting
- Refactory MyQuery

### Fixed
- Jadawl pelayan page jadwal 2020 not display currently
- Auto generate tahun support for swict 'bulan' and 'tahun'

## [2.3.4] - 2021-01-25
### Added
- robot.txt and sitemap
- Aplication path APP_PATH['url'], (models, view, controller, services, component)

### Changed
- Auth login default change to lavel 3

### Security  
- Naming session
- Auth login level incress to 3 Must match IP, user agen and user

## [2.3.3] - 2021-01-25
### Added 
- Steal Tailwind-color (gray, blue)
- MyModel more mudular and complex filter

### Changed 
- MedicalRecords now extends MyModel
- Search rm defult search now is strict mode
- Some ui improvement

### Fixed
- New Post QnA redirect to wrong url when sumbited new post/question
- CLI simpus help command not updated
- Meta html qna not relevant
- Add no caching to some api in new rm
- Search rm showing minus number of index
- Sort order view and search rm not sorted correctly
- Result not found display not work correctly in search and view rm

## [2.3.2] - 2021-01-22
### Added 
- Add new CLI command to add models and model (ver 0.2)
- Add new feature QNA (system forum tanya jawab)
- Add SCRF protection (Captcha form)
- Menambah library Alpine.js

## [2.3.1] - 2021-01-08
### Added
- Support Cache Controll
- Add Request and Respone class, to handle header (reqest and respone)
- MyPDO DB_NAME can access from .env (config)

### Fixed
- Prevent double upload, Story upload delay when upload data on progress

### Removed
- ConvertCode (localy), ConvertCode vendor is keeped

## [2.3.0] - 2021-01-05
### Added
- Support structural file config (individual)

### Changed 
- Folder structur lib/js/ to resources/js/
- Folder structur lib/scss/ to resources/sass/
- Folder structur lib/component/ to resources/component/
- Folder structur lib/apps/ to app/ (incclude controller, view, services, library, and more)

### Deprecated
- Folder structur app/library/Model to app/Models/
- Folder structur app/library/Simpus/Helper to app/library/Helper

## [2.2.0] - 2020-11-27
### Added
- Private folder / single entry point
- Support Nodejs Modules
- Support Unit Test
- Support Github Sction
- Support CLI (make controller, view, models)

## [2.1.0] - 2020-06-30
### Added
- MVC Supported
- Config File
- Support Composser
- Support Composser dependency
- Suopport Sass

## [2.0.0] - 2020-03-21
### Added
- Support pretty url
- Support mini mvc

## [1.0.0] - 2020-03-21
### Added
- This poject start by [Angger Pradana](https://github.com/SonyPradana)

