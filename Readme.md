## core-modules
core-modules is a Laravel package that streamlines modular development by organizing models, controllers, migrations, and policies under a clean namespace like Core/. It simplifies scaffolding and improves code separation for better project structure.

## 🚀 Features
- Modular resource generation 
- Creates models, controllers, migrations, and policies in namespaced structure
- Improves project organization and maintainability
- Ideal for large-scale or domain-driven Laravel applications

## 🛠 Project Configuration
```bash
composer require raza9798/core-modules
php artisan module:configure
php artisan module:make {module} {ResourceName}

```

## 📁 Directory Structure
```
app/
├── Models/
│   └── xx/
│       └── xxxx.php
├── Http/
│   └── Controllers/
│       └── xx/
│           └── xxxController.php
├── Policies/
│       └── xx/
│           └── xxxxPolicy.php

database/
└── migrations/
│       └── xx/
│           └── xxxx_xx_xx_create_xxxx_table.php

```
