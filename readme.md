### EDU DEMO

```
composer config:
        "post-install-cmd": [
            "php artisan clear-compiled",
            "chmod -R 777 storage",
            "echo -n $OAUTH_PRIVATE_KEY > storage/oauth-private.key",
            "echo -n $OAUTH_PUBLIC_KEY > storage/oauth-public.key"
        ]
```