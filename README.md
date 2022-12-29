# phrase-translation-bundle
providing some commands & services to help you manage your translation keys at phrase.
this might be especially usefull when you switched to using the [phrase translation provider](https://github.com/wickedOne/phrase-translation-provider).

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require wickedone/phrase-translation-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require wickedone/phrase-translation-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    WickedOne\PhraseTranslationBundle\WickedOnePhraseTranslationBundle::class => ['all' => true],
];
```

#### step 3: configuration
in your `config/packages` directory create a `wickedone.yaml` file with the following content:
```yaml
wicked_one_phrase_translation:
  dsn: '%env(PHRASE_DSN)%'
```
and in your `.env` file define the phrase dsn like so
```dotenv
PHRASE_DSN=phrase://PROJECT_ID:API_TOKEN@default?userAgent=myProject
```

##### dsn elements
- `PROJECT_ID`: can be retrieved in phrase from `project settings > API > Project ID`
- `API_TOKEN`: can be created in your [phrase profile settings](https://app.phrase.com/settings/oauth_access_tokens)
- `default`: endpoint, defaults to `api.phrase.com`

##### dsn query parameters
- `userAgent`: please read [this](https://developers.phrase.com/api/#overview--identification-via-user-agent) for some examples.

## Commands

After installation two new commands will be available to your application:

### `phrase:keys:tag` command

this command helps you to batch tag keys in phrase by querying for existing tags and / or key name.
you can search for multiple tags at once and a broad search on key name using the `*` wildcard.
keep in mind the query is an AND query, meaning the keys have to match all criteria.

**example**:
```bash
php bin/console phrase:keys:tag -k error.* -t ticket-15 -t ticket-13 --tag epic-5
```
this will search for all keys matching the name `error.*` and with tags `ticket-15` AND `ticket-13` and will add the tag `epic-5` to them.

when you add the `--dry-run` option to the command, it will list the first 100 matches to your query.

### `phrase:keys:untag` command

this command helps you to batch remove tags from keys in phrase by querying for existing tags and / or key name.
you can search for multiple tags at once and a broad search on key name using the `*` wildcard.
keep in mind the query is an AND query, meaning the keys have to match all criteria.

**example**:
```bash
php bin/console phrase:keys:untag -k error.* -t ticket-15 -t ticket-13 --tag epic-5
```
this will search for all keys matching the name `error.*` and with tags `ticket-15` AND `ticket-13` and will remove the tag `epic-5` from them.

when you add the `--dry-run` option to the command, it will list the first 100 matches to your query.
