# Getting started

[![Latest Stable Version](https://poser.pugx.org/laraflow/form/v)](//packagist.org/packages/laraflow/form)
[![Total Downloads](https://poser.pugx.org/laraflow/form/downloads)](//packagist.org/packages/laraflow/form)
[![run-tests](https://github.com/laraflow/form/workflows/run-tests/badge.svg)](//github.com/laraflow/form/actions/workflows/run-tests.yml)
[![License](https://poser.pugx.org/laraflow/form/license)](//packagist.org/packages/laraflow/form)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/laraflow/form/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/laraflow/form/?branch=main)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/laraflow/form/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/laraflow/form/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/laraflow/form/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)


## Introduction
This document provides the details related to Remittance API. This APIs is used to initiate payment request from Mobile client/others
exchange house.

## Environments

This package has basic form element style that is supported by bootstrap.
Some basic form styles are given below:
1. **Sandbox/UAT environment**
    - Please provide your environment IP address that needed to be whitelisted in our system.
    - Once your IP is whitelisted you will receive an email with the access credential for test environment.
    - **Endpoint**: http://nrbms.thecitybank.com/nrb_api_test/dynamicApi.php?wsdl
2. **Production environment**
    - The process will remain same to get the production web service access.
    - **Endpoint**: http://nrbms.thecitybank.com/dynamicApi.php?wsdl
    
## Security Vulnerabilities

If you discover a security vulnerability within Form Package,
please send an e-mail to Mohammad Hafijul Islam via [laraflow@gmail.com](mailto:laraflow@gmail.com).
All security vulnerabilities will be promptly addressed.

## Changelog

Please see [CHANGELOG](changelog.md) for more information on what has changed recently.

## License

The Form is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
