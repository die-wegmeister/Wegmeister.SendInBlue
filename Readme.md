# Wegmeister.SendInBlue

## Installation

Add the following part in your global `composer.json` file:

```
{
    ...
    repositories: {
        ...

        "sendinblue": {
            "type": "git",
            "url": "git@bitbucket.org:diewegmeister/wegmeister.sendinblue.git",
            "no-api": true,
            "options": {
                "ssh2": {
                    "username": "git",
                    "pubkey_file": "~/.ssh/id_rsa.pub",
                    "privkey_file": "~/.ssh/id_rsa"
                }
            }
        },

        ...
    },
    "require": {
        ...

        "wegmeister/sendinblue": "^1.0",

        ...
    }
    ...
}
```

Then run the following command to update your dependencies:

```
composer update --no-dev
```

> Don't forget to update the [Access keys](https://bitbucket.org/diewegmeister/wegmeister.sendinblue/admin/access-keys/) in Bitbucket.
