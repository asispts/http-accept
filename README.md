[![Build](https://github.com/asispts/http-accept/actions/workflows/ci.yml/badge.svg)](https://github.com/asispts/http-accept/actions/workflows/ci.yml)
[![Packagist PHP Version](https://img.shields.io/packagist/dependency-v/asispts/http-accept/php)](https://packagist.org/packages/asispts/http-accept)
[![Packagist Version](https://img.shields.io/packagist/v/asispts/http-accept?label=stable)](https://packagist.org/packages/asispts/http-accept)
[![License](https://img.shields.io/github/license/asispts/http-accept)](./LICENSE)


# `http-accept` parser
`http-accept` is a PHP parser designed to handle HTTP headers related to content negotiation. These headers include `Accept`, `Accept-Language`, `Accept-Encoding`, and `Content-Type`. The library provides parser classes for each of these headers, making it easy to extract the relevant information from incoming HTTP requests.

## Installation
You can install this library using [composer](https://getcomposer.org/).
```bash
composer require asispts/http-accept
```

## Usage
To parse the different headers, use the corresponding parser class.

### Parse `Content-Type`
```php
$contentType = (new ContentTypeParser())->parse($source);
```

### Parse HTTP `Accept`
```php
$types = (new AcceptParser())->parse($source);
```

### Parse `Accept-Language`
```php
$languages = (new AcceptLanguageParser())->parse($source);
```

### Parse `Accept-Encoding`
```php
$encodings = (new AcceptEncodingParser())->parse($source);
```

## Contributing
All forms of contributions are welcome, including bug reports, feature requests, and pull requests. If you plan to make major changes, please open an issue first to discuss what you would like to change.

## License
Released under [Apache-2.0 License](https://opensource.org/licenses/Apache-2.0). See [LICENSE](./LICENSE) file for more details.
