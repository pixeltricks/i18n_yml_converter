# Translation Process #

## Overview ##

SilverStripe contains language files for user-facing strings (see [i18n](/topics/i18n)).
These are stored in YML format, which is fairly readable,
but also sensitive to whitespace and formatting changes,
so not ideally suited for non-technical editors.

Note: Until SilverStripe 3.0, we used a PHP storage format.
This format is now deprecated, and we don't provide tools
for editing the files. Please see below for information on
how to convert these legacy files and existing translations to YML.

## Collecting translatable text ##

As a first step, you can automatically collect
all translatable text in your module through the `i18nTextCollector` task.
See [i18n](/topics/i18n#collecting-text) for more details.

## 

### 1. Convert lang files from PHP to YML

All modules: `sake dev/tasks/i18nYMLConverterTask`
Single module: `sake dev/tasks/i18nYMLConverterTask module=<mymodule>`
	
### 2. Create getlocalization.com account



### 3. Import master files

On the "Files" tab, you can choose "Import from SCM",
and connect getlocalization to your github account.
Alternatively, upload the `en.yml` file in the "Ruby on Rails" format.

### 4. Import translations

While you can do this through the UI, it can get a bit tedious
uploading 50+ files.



## Conversion from 2.4 PHP format

The conversion from PHP format to YML is taken care of by a module
called [i18n_yml_converter](https://github.com/chillu/i18n_yml_converter).

## Contact

Translators have their own [mailinglist](https://groups.google.com/forum/#!forum/silverstripe-translators),
but you can also reach a core member on [IRC](http://silverstripe.org/irc).
The getlocalization.com interface has a built-in discussion board if
you have specific comments on a translation.