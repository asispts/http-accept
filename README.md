<div align="center">
  <samp>
    <h1>http-accept</h1>
    <h3>&raquo; PHP parser for HTTP content negotiation headers  &laquo;</h3>
  </samp>
  &nbsp;
</div>

[![Build](https://github.com/asispts/http-accept/actions/workflows/ci.yml/badge.svg)](https://github.com/asispts/http-accept/actions/workflows/ci.yml)
[![License](https://img.shields.io/github/license/asispts/http-accept)](./LICENSE)
[![PHP Version](https://img.shields.io/packagist/dependency-v/asispts/http-accept/php)](https://packagist.org/packages/asispts/http-accept)
[![Stable Version](https://img.shields.io/packagist/v/asispts/http-accept?label=stable)](https://packagist.org/packages/asispts/http-accept)
[![Downloads](https://img.shields.io/packagist/dt/asispts/http-accept)](https://packagist.org/packages/asispts/http-accept)


`http-accept` is a PHP library for parsing HTTP headers used in content negotiation. It supports the following headers:
- `Accept`
- `Accept-Language`
- `Accept-Encoding`
- `Content-Type`

Parser classes are provided for each header, making it straightforward to extract information from incoming requests.


## Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require asispts/http-accept
```

## Usage

Use the corresponding parser class for each header.

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

Contributions are welcome—whether bug reports, feature requests, or pull requests.
For major changes, please open an issue first to discuss your ideas.

---

## License

Licensed under the [Apache-2.0 License](https://opensource.org/licenses/Apache-2.0).
See the [LICENSE](./LICENSE) file for details.
