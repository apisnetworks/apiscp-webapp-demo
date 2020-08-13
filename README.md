# ApisCP sample application

This is a sample application for [ApisCP](https://apiscp.com).

## Installation

```bash
cd /usr/local/apnscp
git clone https://github.com/apisnetworks/apiscp-webapp-demo config/custom/webapps/demo
./composer dump-autoload -o
```
Edit config/custom/boot.php, create if not exists:

```php
<?php
	\a23r::registerModule('demo2', \apisnetworks\demo\Demo_Module::class);
	\Module\Support\Webapps::registerApplication('demo', \apisnetworks\demo\Demo::class);
```

Then restart ApisCP.

```bash
systemctl restart apiscp
```

Voila!

## Learning more
All third-party documentation is available via [docs.apiscp.com](https://docs.apiscp.com/admin/webapps/Custom/).
