# php-detectLang
a simple language detection library for php + mysql

## Requirement
php-detectLang works on php 5.0 or higher & mysql environment. It uses mysqli and fopen api.

## Installation
First, you should make own database for php-detectLang, and write information of db connect into conf/db.ini file.
```[db]
host = "localhost"
id = "Your db username"
pw = "Your db password"
db = "Your db name"
```
Next, you should run install/init_basic.sql to install data into your database. If you want to detect more languages, run install/init_advanced.sql too.

## Documentation

### Class detectLang 
This is a main class of php-detectLang library. To use this library, you should instantiate detectLang class first.

#### __constructor()
detectLang::__constructor has no parameters.

### function detect($text, $max_result = 10)
detectLang::detect has two parameters.
- $text : the text to be detected. It must be encoded in utf-8.
- $max_result : the maximum number of results. If you omit it, the function returns 10 results.

This function returns a similarity between input text and languages as array. An element of the returned array has 3 keys.
- code : language code matched input text
- language : language name matched input text
- weight : matched weight. The higher weight means the more possible.
The returned array is sorted by weights in descendant order. If the detection fails or an error occurs, the function returns false.

## Example
```<?php
require 'src/detectLang.php';
$dl = new detectLang();
$res = $dl->detect('This is an English sentence written by someone, and that is not a Korean sentence.');
if($res) print_r($res[0]['language']);
```

## Supported language

### Basic set
If you install only init_basic.sql, you can detect these languages.
- Afrikaans (Afrikaans)
- العربية (Arabic)
- azərbaycanca (Azerbaijani)
- беларуская (Belarusian)
- български (Bulgarian)
- བོད་ཡིག (Tibetan)
- català (Catalan)
- čeština (Czech)
- dansk (Danish)
- Deutsch (German)
- Ελληνικά (Greek)
- English (English)
- Esperanto (Esperanto)
- español (Spanish)
- eesti (Estonian)
- euskara (Basque)
- فارسی (Persian)
- suomi (Finnish)
- français (French)
- galego (Galician)
- עברית (Hebrew)
- हिन्दी (Hindi)
- hrvatski (Croatian)
- magyar (Hungarian)
- Հայերեն (Armenian)
- Bahasa Indonesia (Indonesian)
- italiano (Italian)
- 日本語 (Japanese)
- 한국어 (Korean)
- македонски (Macedonian)
- Nederlands (Dutch)
- polski (Polish)
- português (Portuguese)
- română (Romanian)
- русский (Russian)
- srpskohrvatski / српскохрватски (Serbo-Croatian)
- slovenčina (Slovak)
- српски / srpski (Serbian)
- svenska (Swedish)
- Kiswahili (Swahili)
- தமிழ் (Tamil)
- తెలుగు (Telugu)
- тоҷикӣ (Tajik)
- ไทย (Thai)
- Türkmençe (Turkmen)
- Tagalog (Tagalog)
- татарча/tatarça (Tatar)
- ئۇيغۇرچە / Uyghurche (Uyghur)
- українська (Ukrainian)
- Tiếng Việt (Vietnamese)
- 中文 (Chinese)

### Advanced set
If you install not only init_basic.sql but also init_advanced.sql, you can detect 230 languages. But the accuracy of detection in some languages could be low.
