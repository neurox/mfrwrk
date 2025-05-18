![PHP Version](https://img.shields.io/badge/PHP-8%2E2+-blue) ![Node JS Version](https://img.shields.io/badge/Node_JS-20%2E12%2E2+-2c682c) ![SQLite Version](https://img.shields.io/badge/SQLite-3+-1080cc) ![License](https://img.shields.io/github/license/neurox/mfrwrk)

## Create Password for a user.
```sh
echo password_hash('my_secure_password', PASSWORD_DEFAULT);
```

## From terminal with docksal
```sh
fin exec php -r "echo password_hash('my_secure_password', PASSWORD_DEFAULT) . PHP_EOL;"
```

## Process styles
```sh
fin exec gulp
```

## Manage modules.

# List all available modules
```sh
fin module list
```

# Install a module
```sh
fin module install [module_name]
```

# Uninstall a module
```sh
fin module uninstall [module_name]
```
