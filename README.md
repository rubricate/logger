# RubricatePHP Logger

[![Maintainer](http://img.shields.io/badge/maintainer-@estefanionsantos-blue.svg?style=flat-square)](https://estefanionsantos.github.io/)
[![Source Code](http://img.shields.io/badge/source-rubricate/logger-blue.svg?style=flat-square)](https://github.com/rubricate/logger)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/rubricate/logger.svg?style=flat-square)](https://packagist.org/packages/rubricate/logger)
[![Latest Version](https://img.shields.io/github/release/rubricate/logger.svg?style=flat-square)](https://github.com/rubricate/logger/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/rubricate/logger.svg?style=flat-square)](https://packagist.org/packages/rubricate/logger)

A lightweight activity logger to monitor user actions across your website or application

#### Last Version
```
$ composer require rubricate/logger
```
## About RubricatePHP Logger

**RubricatePHP Logger** is an architectural foundation for tracking and monitoring user activity within your web applications. Built with extensibility and clean code principles in mind, it provides an abstract backbone that allows developers to seamlessly implement custom logging drivers—whether you need to store logs in local files, relational databases, or external monitoring systems.

### Key Features

* **Architectural Freedom:** Powered by a clean `AbstractLogger` base class, enforcing robust design patterns across your logging infrastructure.
* **Zero Bloat:** Extremely lightweight with zero external dependencies, keeping your application fast and unburdened.
* **Extensible Drivers:** Easily build or swap concrete loggers to monitor page views, user actions, or critical system events based on your project's needs.
* **Ecosystem Ready:** Fully compatible and optimized to integrate smoothly with other Rubricate components.

- Documentation is at https://rubricate.github.io/components/logger
- issues: https://github.com/rubricate/logger/issues

### Credits
- [All Contributors](https://github.com/rubricate/logger/contributors) (Let's program)

### License

The MIT License (MIT). Please see [License File](https://github.com/rubricate/logger?tab=MIT-1-ov-file) for more information.
