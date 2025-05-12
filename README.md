

## Create Password for a user.
echo password_hash('my_secure_password', PASSWORD_DEFAULT);

## From terminal with docksal
fin exec php -r "echo password_hash('my_secure_password', PASSWORD_DEFAULT) . PHP_EOL;"

## Process styles
fin exec gulp

## Manage modules.

# List all available modules
fin module list

# Install a module
fin module install [module_name]

# Uninstall a module
fin module uninstall [module_name]
