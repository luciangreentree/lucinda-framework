# Lucinda Framework

Table of contents:

- [About](#about)
- [Installation](#installation)
   - Setting virtual host
   - Configuration
- Project Structure
- [Documentation](#documentation)

## About

Lucinda Framework 3.0 is an ultra high performance web application skeleton developed with simplicity and modularity at its foundation. In order to fulfil these goals, it functions as an XML-based contract of completely independent APIs, each designed for a particular aspect of a web application's logic:

| API | Description |
| --- | --- |
| [STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0) | handles HTTP requests into responses using MVC paradigm |
| [STDERR MVC API](https://github.com/aherne/errors-api/tree/v2.0.0) | handles errors or uncaught exceptions into reports and responses using MVC paradigm |
| [View Language API](https://github.com/aherne/php-view-language-api/tree/v3.0.0) | templates HTML views using a language extending HTML standard, similar to Java's EL & JSTL |
| [Logging API](https://github.com/aherne/php-logging-api/tree/v3.0.0) | logs messages/exceptions to a storage medium |
| [SQL Data Access API](https://github.com/aherne/php-sql-data-access-api/tree/v3.0.0) | connects to SQL vendors (eg: MySQL), executes queries and parses results on top of PDO |
| [NoSQL Data Access API](https://github.com/aherne/php-nosql-data-access-api/tree/v3.0.0) | connects to NoSQL key-value stores (eg: Redis), executes operations (eg: get) and retrieves results |
| [Web Security API](https://github.com/aherne/php-security-api/tree/v3.0.0) | performs authentication and authorization on different combinations |
| [OAuth2 Client API](https://github.com/aherne/oauth2client/tree/v3.0.0) | communicates with OAuth2 Providers (eg: Facebook) in order to retrieve remote resources owned by client |
| [HTTP Headers API](https://github.com/aherne/headers-api) | encapsulates HTTP Request/Response headers according to ISO standards and applies Cache/CORS validation when demanded |
| [Internationalization API](https://github.com/aherne/php-internationalization-api/tree/v3.0.0) | makes it possible for HTML views to be translated automatically to client's locale |

Framework logic thus becomes strictly one of integrating all above functionalities for a common goal, that of providing an integrated platform for programmers to start developing on. To further modularity and ease of update even further, framework itself is broken into THREE APIs, each with its own repo:

| API | Description |
| --- | --- |
| [Framework Skeleton API](https://github.com/aherne/lucinda-framework/tree/v3.0.0) | contains  "mobile" part of framework logic, the project skeleton developers will start working on once framework was installed. Once installed, logic inside becomes owned by developers, so it cannot be updated. |
| [Framework Engine API](https://github.com/aherne/lucinda-framework-engine/tree/v2.0.0) | contains  "fixed" part of framework logic, from binding classes required in APIs "conversation as well to general purpose classes. All logic inside is owned by framework, so open for composer update but not open for change by developers. |
| [Framework Configurer API](https://github.com/aherne/lucinda-framework-configurer/tree/v2.0.0) | contains console step-by-step configurer as well as files going to be copied on developers' project once process completes. All logic inside is owned by framework, as in above, but choices of configuration are in developer's hands. |

As its composing APIs, framework is PHP 7.1+ and PSR4 autoload compliant, using unit testing (for engine) as well as functional testing (for skeleton and configurer) to insure stability.

## Installation

To install framework, open console and run:

```console
cd YOUR_WEB_ROOT
git clone -b v3.0.0 https://github.com/aherne/lucinda-framework YOUR_PROJECT_NAME
cd YOUR_PROJECT_NAME
composer update
```

After you've finished [setting virtual host](#setting-virtual-host) pointing to YOUR_WEB_ROOT/YOUR_PROJECT_NAME, open your browser at http://YOUR_HOST_NAME and follow steps described there to configure your project.

In order to enforce performance and modularity, **project starts with MVC abilities only**! To bind any other ability your project requires (eg: querying MySQL databases), use step-by-step console configurer below:

```console
cd YOUR_WEB_ROOT/YOUR_PROJECT_NAME
php configure.php project
```

Options given by configurer, however, are limited for most common scenarios. If your scenario is not covered, doing it manually based on **[documentation](#documentation)** may be your only choice!

### Setting Virtual Host

Now you will need a virtual host that makes sure YOUR_HOST_NAME points to YOUR_WEB_ROOT/YOUR_PROJECT_NAME, so first open .hosts file (/etc/hosts @ Unix, C:\Windows\System32\drivers\etc\hosts @ Windows) and add this line:

```console
127.0.0.1 YOUR_HOST_NAME
```

After you're done setting virtual host (see below), restart web server and you will be able to see homepage at: http://YOUR_HOST_NAME

#### Apache2

If you're using **Apache2**, after you've made sure *mod_rewrite* and *mod_env* are enabled, open general vhosts configuration file or create a separate vhost file then write:

```console
<VirtualHost *:80>
    # sets site domain name (eg: www.testing.local)
    ServerName YOUR_HOST_NAME
    # sets location of site on disk (eg: /var/www/html/testing)
    DocumentRoot YOUR_WEB_ROOT/YOUR_PROJECT_NAME
    # delegates rerouting to htaccess file above
    <Directory YOUR_WEB_ROOT/YOUR_PROJECT_NAME>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

This enables .htaccess in project root, which has following lines:

```console
# informs Apache2 web server you are going to reroute requests
RewriteEngine on
# turns off directory listing
Options -Indexes
# makes 404 responses to public (images, js, css) files handled by web server
ErrorDocument 404 default
# lets web server allow Authorization request header
RewriteRule .? - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
# redirects all requests, except those pointing to public files, to bootstrap
RewriteCond %{REQUEST_URI} !^/public
RewriteCond %{REQUEST_URI} !^/favicon.ico
RewriteRule ^(.*)$ index.php
# sets development environment (default is local)
SetEnv ENVIRONMENT local
```

#### NGINX

If you're using **NGINX**, open general vhosts configuration file or create a separate vhost file then write:

```console
server {
    listen 80;
    listen [::]:80 ipv6only=on;
    # sets location of site on disk (eg: /var/www/html/testing)
    root YOUR_WEB_ROOT/YOUR_PROJECT_NAME;
    # sets location of bootstrap file
    index index;
    # sets site domain name (eg: www.testing.local)
    server_name YOUR_HOST_NAME;
    # redirects all requests, except those pointing to public files, to bootstrap
    location / {
        rewrite ^/(.*)$ /index;
    }
    location /public/ {
    }
    location /favicon.ico {
    }
    # configures PHP-FPM to handle requests
    location ~ \$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\)(/.+)$;
        # location of PHP-FPM socket file (eg: /var/run/php/php7.0-fpm.sock)
        fastcgi_pass unix:SOCKET_FILE_LOCATION;
        fastcgi_index index;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SERVER_ADMIN NA;
        fastcgi_param SERVER_SIGNATURE nginx/$nginx_version;
        include fastcgi_params;
    }
}
```

To set development environment, you need to edit PHP-FPM configuration file (eg: /etc/php/7.2/fpm/php-fpm.conf) then add *local* environment:

```console
env[ENVIRONMENT] = local
```

## Documentation

As one can see in [Apache2](#apache2) or [NGINX](#NGINX) sections above, in order to manage the whole application, all requests to your project, except those pointing to static files (folder /public, favicon.ico), are *re-routed* to **[index.php](https://github.com/aherne/lucinda-framework/blob/v3.0.0/index.php)** file.

This file's job is only to start the framework. Framework itself is the marriage contract between [STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0) and [STDERR MVC API](https://github.com/aherne/errors-api/tree/v2.0.0), handling request-response and exception-response flows, with an ability of other APIs (functionalities) to be hooked [declaratively](#declarative-integration) (through XML) or at [runtime](#programmatic-integration) (through event listeners). In light of above, bootstrap only:

* loads composer autoloader
* detects development environment registered on web server (see ) into **ENVIRONMENT** constant
* registers [STDERR MVC API](https://github.com/aherne/errors-api/tree/v2.0.0) as handler of exception-response flow based on:
   * **[stderr.xml](https://github.com/aherne/lucinda-framework/blob/v3.0.0/stderr.xml)** file where API is configured
   * **ENVIRONMENT** detected above
   * **[EmergencyHandler](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/models/EmergencyHandler.php)** instance to handle errors that may occur during handling process itself
* lets above stand in suspended animation unless an error/exception is thrown below
* registers [STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0) as handler of request-response flow based on:
   * **[stdout.xml](https://github.com/aherne/lucinda-framework/blob/v3.0.0/stdout.xml)** file where API is configured
   * **ENVIRONMENT** detected above
   * **[Attributes](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/models/Attributes.php)** instance to make values set by event listeners available to controllers or subsequent event listeners
* registers event listeners that execute when a [STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0) lifecycle event is reached
   * **[ErrorListener](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/listeners/ErrorListener.php)** event is registered by default,  informing [STDERR MVC API](https://github.com/aherne/errors-api/tree/v2.0.0) to use same response content type as resource requested
* starts request-response handling

### Declarative Integration

MVC APIs have some tags holding attributes that point to class names and paths. Each of them offers a potential hook point to join other APIs or developers' own implementation:

| API | XML Tag | Tag Attribute | Class Prototype | Description |
| --- | --- | --- | --- | --- |
| [STDERR MVC API](https://github.com/aherne/errors-api/tree/v2.0.0) | [reporter](https://github.com/aherne/errors-api#reporters) | class | [Lucinda\STDERR\Reporter](https://github.com/aherne/errors-api#abstract-class-reporter) | reports error to a storage medium |
| [STDERR MVC API](https://github.com/aherne/errors-api/tree/v2.0.0) | [resolver](https://github.com/aherne/errors-api#resolvers) | class | [Lucinda\STDERR\ViewResolver](https://github.com/aherne/errors-api#abstract-class-viewresolver) | resolves view into response by format |
| [STDERR MVC API](https://github.com/aherne/errors-api/tree/v2.0.0) | [exceptions/exception](https://github.com/aherne/errors-api#exceptions) | controller | [Lucinda\STDERR\Controller](https://github.com/aherne/errors-api#abstract-class-controller) | handles an exception route |
| [STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0) | [format](https://github.com/aherne/php-servlets-api#formats) | class | [Lucinda\STDOUT\ViewResolver](https://github.com/aherne/php-servlets-api#abstract-class-viewresolver) | resolves view into response by format |
| [STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0) | [route](https://github.com/aherne/php-servlets-api#routes) | controller | [Lucinda\STDOUT\Controller](https://github.com/aherne/php-servlets-api#abstract-class-controller) | handles an URI route |
| [STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0) | [session](https://github.com/aherne/php-servlets-api#session) | handler | [\SessionHandlerInterface](https://www.php.net/manual/en/class.sessionhandlerinterface.php) | handles session by storage medium |

Whatever is hooked there will be integrated by MVC APIs automatically, whenever interpreter reaches respective section. APIs to integrate, on the other hand, provide their own hook points as well:

| API | XML Tag | Tag Attribute | Class Prototype | Description |
| --- | --- | --- | --- | --- |
| [Logging API](https://github.com/aherne/php-logging-api/tree/v3.0.0) | [logger](https://github.com/aherne/php-logging-api/tree/v3.0.0#configuration) | class | [Lucinda\Logging\AbstractLoggerWrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/AbstractLoggerWrapper.php) | writes message/exception to a storage medium |
| [Web Security API](https://github.com/aherne/php-security-api/tree/v3.0.0) | [form](https://github.com/aherne/php-security-api/tree/v3.0.0#security) | dao | [Lucinda\WebSecurity\Authentication\DAO\UserAuthenticationDAO](https://github.com/aherne/php-security-api/blob/v3.0.0/src/Authentication/DAO/UserAuthenticationDAO.php) | authenticates user in database by form |
| [Web Security API](https://github.com/aherne/php-security-api/tree/v3.0.0) | [form](https://github.com/aherne/php-security-api/tree/v3.0.0#security) | throttler | [Lucinda\WebSecurity\Authentication\Form\LoginThrottler](https://github.com/aherne/php-security-api/blob/v3.0.0/src/Authentication/Form/LoginThrottler.php) | throttles failed login attempts against brute-force attacks |
| [Web Security API](https://github.com/aherne/php-security-api/tree/v3.0.0) | [oauth2](https://github.com/aherne/php-security-api/tree/v3.0.0#security) | dao | [Lucinda\WebSecurity\Authentication\OAuth2\VendorAuthenticationDAO](https://github.com/aherne/php-security-api/blob/v3.0.0/src/Authentication/OAuth2/VendorAuthenticationDAO.php) | authenticates user in database by oauth2 provider |
| [Web Security API](https://github.com/aherne/php-security-api/tree/v3.0.0) | [by_dao](https://github.com/aherne/php-security-api/tree/v3.0.0#security) | page_dao | [Lucinda\WebSecurity\Authorization\DAO\PageAuthorizationDAO](https://github.com/aherne/php-security-api/blob/v3.0.0/src/Authorization/DAO/PageAuthorizationDAO.php) | gets rights for route requested in database |
| [Web Security API](https://github.com/aherne/php-security-api/tree/v3.0.0) | [by_dao](https://github.com/aherne/php-security-api/tree/v3.0.0#security) | user_dao | [Lucinda\WebSecurity\Authorization\DAO\UserAuthorizationDAO](https://github.com/aherne/php-security-api/blob/v3.0.0/src/Authorization/DAO/PageAuthorizationDAO.php) | authorizes user to route requested in database |
| [HTTP Headers API](https://github.com/aherne/headers-api) | [headers](https://github.com/aherne/headers-api#headers) | cacheable* | [Lucinda\Headers\Cacheable](https://github.com/aherne/headers-api/blob/master/src/Cacheable.php) | generates ETag and LastModified header values for response |

\*: attribute added by Lucinda framework

## Programmatic Integration

STDOUT MVC API also allows you to manually integrate APIs or user code through [event listeners](https://github.com/aherne/php-servlets-api#binding-events) in **[index.php]()** file via *addEventListener* method. Syntax:

```php
$object->addEventListener(EVENT_TYPE, CLASS_NAME);
```

Following event types are available:

| Event Type | Description |
| --- | --- |
| Lucinda\STDOUT\EventType::START	| Before stdout.xml is read. |
| Lucinda\STDOUT\EventType::APPLICATION	| After stdout.xml is read, before request is read |
| Lucinda\STDOUT\EventType::REQUEST | After request is read, before controller runs |
| Lucinda\STDOUT\EventType::RESPONSE | After view resolver runs, before response is outputted |
| Lucinda\STDOUT\EventType::END | After response is outputted |

 Event listeners will be *run* in the order they are set once respective lifecycle event is reached.

## Project Structure

Any project using this framework will use following file/folder structure:

* **.htaccess**: Apache2 configuration file
* **composer.json**: framework or your project composer dependencies definitions
* **configure.php**: step-by-step console configurer adapting framework to your project needs
* **index.php**: bootstrap PHP file starting framework
* **stderr.xml**: configures [STDERR MVC API](https://github.com/aherne/errors-api/tree/v2.0.0) to manage exception-response flow. 			
* **stdout.xml**: configures [STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0) to manage request-response flow.
* **application**: contains framework skeleton logic and user code built on its foundation
* **compilations**: contains [compilations](https://github.com/aherne/php-view-language-api/tree/v3.0.0#compilation) for [View Language API](https://github.com/aherne/php-view-language-api/tree/v3.0.0)
* **public**: contains your static project files (images, js, css)
* **vendor**: contains APIs pulled based on **composer.json** above

### How Framework Connects Hook Points

Framework's job is to know of all available hook points and plug them whenever needed. It comes with a list of folders, each one storing a list of implementation of a hook point described in [automatic integration](#automatic-integration) section:

| Prototype | Folder | Classes | Binding To |
| --- | --- | --- | --- |
| [Lucinda\Headers\Cacheable](https://github.com/aherne/headers-api/blob/master/src/Cacheable.php) | [application/cacheables](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/cacheables/) | DateCacheable<br/>EtagCacheable | [NoSQL Data Access API](https://github.com/aherne/php-nosql-data-access-api/tree/v3.0.0)<br/>[STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0) |
| [Lucinda\Logging\AbstractLoggerWrapper](https://github.com/aherne/php-logging-api/blob/v3.0.0/src/AbstractLoggerWrapper.php) | [application/loggers](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/loggers/) | FileLogger<br/>SyslogLogger | [Logging API](https://github.com/aherne/php-logging-api/tree/v3.0.0) |
| [Lucinda\STDERR\Reporter](https://github.com/aherne/errors-api#abstract-class-reporter) | [application/reporters](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/reporters/) | FileReporter<br/>SyslogReporter | [Logging API](https://github.com/aherne/php-logging-api/tree/v3.0.0) |
| [Lucinda\STDERR\ViewResolver](https://github.com/aherne/errors-api#abstract-class-viewresolver) | [application/renderers](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/renderers/) | HtmlRenderer<br/>JsonRenderer | [View Language API](https://github.com/aherne/php-view-language-api/tree/v3.0.0)<br/>[Lucinda\Framework\Json](https://github.com/aherne/lucinda-framework-engine/tree/v2.0.0#json) |
| [Lucinda\STDERR\Controller](https://github.com/aherne/errors-api#abstract-class-controller) | [application/controllers](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/controllers/) | ... | user code |
| [Lucinda\STDOUT\ViewResolver](https://github.com/aherne/php-servlets-api#abstract-class-viewresolver) | [application/resolvers](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/resolvers/) | HtmlResolver<br/>JsonResolver | [View Language API](https://github.com/aherne/php-view-language-api/tree/v3.0.0)<br/>[Lucinda\Framework\Json](https://github.com/aherne/lucinda-framework-engine/tree/v2.0.0#json) |
| [Lucinda\STDOUT\Controller](https://github.com/aherne/php-servlets-api#abstract-class-controller) | [application/controllers](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/controllers/) | ... | user code |
| [\SessionHandlerInterface](https://www.php.net/manual/en/class.sessionhandlerinterface.php) | [application/handlers](https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/handlers/) | NoSQLSessionHandler | [NoSQL Data Access API](https://github.com/aherne/php-nosql-data-access-api/tree/v3.0.0) |

\*: binding [Logging API](https://github.com/aherne/php-logging-api/tree/v3.0.0) and [NoSQL Data Access API](https://github.com/aherne/php-nosql-data-access-api/tree/v3.0.0) requires LoggingListener and NoSQLDataSourceInjector events below.

#### Event Listeners

Each event listener, described in described in [manual integration](#manual-integration) section, will be found in [application/listeners]((https://github.com/aherne/lucinda-framework/blob/v3.0.0/application/listeners/) folder. Framework comes with following listeners, whose job is mainly to plug APIs in order to add an ability for your project:

| Type | Class | Plugging | Description |
| --- | --- | --- | --- |
| APPLICATION | LoggingListener | [Logging API](https://github.com/aherne/php-logging-api/tree/v3.0.0) | adds logging abilities to your project |
| APPLICATION | SQLDataSourceInjector | [SQL Data Access API](https://github.com/aherne/php-sql-data-access-api/tree/v3.0.0) | adds SQL querying abilities to your project |
| APPLICATION | NoSQLDataSourceInjector | [NoSQL Data Access API](https://github.com/aherne/php-nosql-data-access-api/tree/v3.0.0) | adds NoSQL querying abilities to your project |
| REQUEST | ErrorListener | - | makes errors use same response content type as resource requested |
| REQUEST | SecurityListener | [Web Security API](https://github.com/aherne/php-security-api/tree/v3.0.0) <br/> [OAuth2 Client API](https://github.com/aherne/oauth2client/tree/v3.0.0) | adds authentication & authorization abilities to your project |
| REQUEST | LocalizationListener | [Internationalization API](https://github.com/aherne/php-internationalization-api/tree/v3.0.0) | adds internationalization & localization abilities to your project |
| REQUEST | HttpHeadersListener | [HTTP Headers API](https://github.com/aherne/headers-api) | adds ability to read and generate HTTP headers and use them for validation  |
| REQUEST | HttpCorsListener | - | automatically answers CORS requests made from client's browser. Requires HttpHeadersListener! |
| RESPONSE | HttpCachingListener | - | adds HTTP caching abilities to your project. Requires HttpHeadersListener! |

#### Models



bootstrapping
update views @ configurer to have real see more information

TODO: explain DAO hook points

* **listeners**: contains [event listeners](https://github.com/aherne/php-servlets-api/tree/v3.0.0#binding-events) for [STDOUT MVC API](https://github.com/aherne/php-servlets-api/tree/v3.0.0)
* **models**: contains your project's PHP business logic.
* **tags**: contains [tag libraries](https://www.lucinda-framework.com/view-language/tags) for [View Language API](https://github.com/aherne/php-view-language-api/tree/v3.0.0)
* **views**: contains templates/views for [View Language API](https://github.com/aherne/php-view-language-api/tree/v3.0.0)
