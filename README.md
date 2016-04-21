Active record changelog extension for Yii 2 framework
=====================================================

This extension provides a changelog functional.

For license information check the [LICENSE](https://github.com/rmrevin/yii2-changelog/blob/master/LICENSE)-file.

Support
-------
* [GutHub issues](https://github.com/rmrevin/yii2-changelog/issues)
* [Public chat](https://gitter.im/rmrevin/support)

Installation
------------

The preferred way to install this extension is through [composer](https://getcomposer.org/).

Either run

```bash
composer require "rmrevin/yii2-changelog:~0.1"
```

or add

```
"rmrevin/yii2-changelog": "~0.1",
```

to the `require` section of your `composer.json` file.

Execute migrations:
```
php yii migrate --migrationPath=@rmrevin/yii/changelog/migrations
```

Usage
-----

To view the history, you can use a special panel for debug.
Or make your own section to view the data in your administration panel.

To enable the debug panel, add the following code in the module configuration debug.
```php
    'modules' => [
        // ...
        'debug' => [
            'class' => yii\debug\Module::className(),
            'panels' => [
                rmrevin\yii\changelog\debug\panels\ChangelogPanel::class,
            ],
        ],
    ],

```

For `ActiveRecord` models for which you want to track changes,
you must implement the interface `rmrevin\yii\changelog\interfaces\LoggableInterface`
and add the behavior of `rmrevin\yii\changelog\behaviors\ChangelogBehavior`.

Example:
```php
<?php

use yii\db\ActiveRecord;
use rmrevin\yii\changelog\interfaces\LoggableInterface;
use rmrevin\yii\changelog\behaviors\ChangelogBehavior;

/**
 * Class ShopItem
 *
 * @property integer $id
 * @property integer $number
 * @property string $title
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $synchronized_at
 */
class ShopItem extends ActiveRecord implements LoggableInterface
{

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return '[' . $this->number . '] ' . $this->title;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            // ...
            [
                'class' => ChangelogBehavior::class,
                'ignoreAttributes' => [ // these attributes are not tracked
                    'updated_at',
                    'synchronized_at',
                ],
            ],
        ];
    }
}
```

Done
----
Now when you try to create, modify or delete an instance of a model `ShopItem`
in the table `%changelog` will be recorded relevant information.