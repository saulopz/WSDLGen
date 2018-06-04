# WSDLGen

**Created by Saulo Popov Zambiasi**.
**Version 20170427**.

WSDLGen is a simple generator of wsdl file for PHP. This software and it's source code are available under BSD (3-clause) license. I create this tool as a simple solution for my own projects.

## Prerequisites

* PHP >= 5
* php-soap

## Usage

A example01.php is a example of web service im PHP that shows how use WSDLGen. In the folder that web service file (example01.php) is necessary a wsdl subfolder with writting permition to create wsdl/example01.wsdl .

If you update web service, erase file wsdl/example01.wsdl. It is because the WSDL file only will be created if they not exists to not consume resources.



