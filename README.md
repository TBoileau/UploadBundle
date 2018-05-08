# TBoileau/UploadBundle

[![Build Status](https://travis-ci.org/TBoileau/UploadBundle.svg?branch=master)](https://travis-ci.org/TBoileau/UploadBundle) 
[![Maintainability](https://api.codeclimate.com/v1/badges/c62bafc0d9ce028e5798/maintainability)](https://codeclimate.com/github/TBoileau/UploadBundle/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/c62bafc0d9ce028e5798/test_coverage)](https://codeclimate.com/github/TBoileau/UploadBundle/test_coverage)
[![Latest Stable Version](https://poser.pugx.org/tboileau/upload-bundle/v/stable)](https://packagist.org/packages/tboileau/upload-bundle)
[![Total Downloads](https://poser.pugx.org/tboileau/upload-bundle/downloads)](https://packagist.org/packages/tboileau/upload-bundle)
[![License](https://poser.pugx.org/tboileau/upload-bundle/license)](https://packagist.org/packages/tboileau/upload-bundle)

## Requirements 

* Symfony 4
* PHP 7.1.3 or +
* jQuery

## Summary

* [Installation](#<a-name="1"></a>installation)
* [Configuration](#<a-name="2"></a>configuration)
* [Usage](#<a-name="3"></a>usage)

## <a name="1"></a>Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require tboileau/upload-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
<?php
// config/bundles.php

return [
    //...
    TBoileau\UploadBundle\TBoileauUploadBundle::class => ['all' => true],
    //...
];

```

## <a-name="2"></a>Configuration

### Step 1 : Add new configuration's yaml file

Create a new yaml file in `config/package` directory, name it `t_boileau_upload.yaml` :

```yaml
# config/packages/t_boileau_upload.yaml
t_boileau_upload:
    upload_dir: '%kernel.project_dir%/public/uploads'
    web_path: '/public/uploads'
```

* *upload_dir* : The directory that contain all uploaded files.
* *web_path* : The prefix of the relative path of uploaded files.

### Step 2 : Add routing configuration

Add the routing configuration in `config/routes.yaml` :

```yaml
# config/routes.yaml
t_boileau_upload_bundle:
    resource: '@TBoileauUploadBundle/Controller'
    type:     annotation
    prefix:   /t_boileau_upload

```

Don't forget to install assets.

Use the symlink on development environment :
```console
php bin/console assets:install --symlink
```

Use the copy on production environment :
```console
php bin/console assets:install public
```

### Extra : Override the widget

An example of actual widget :
```twig
{% block upload_widget -%}
    {{-block("hidden_widget") -}}
    <div class="upload-box" data-rel="{{ form.vars.id }}">
        <div class="upload-box-input">
            <input type="file" class="upload-box-file" id="file-{{ form.vars.id }}" data-rel="{{ form.vars.id }}"/>
            <svg class="upload-box-icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"></path></svg>
            <label for="file-{{ form.vars.id }}" class="upload-box-label"></label>
        </div>
        <div class="upload-box-uploading">
            <span class="upload-box-uploading-text"></span>
            <div class="upload-box-progressbar"><div class="upload-box-progress"></div></div>
        </div>
        <div class="upload-box-success">

        </div>
        <div class="upload-box-error"></div>
    </div>
{%- endblock %}
```

You can keep only the input file like this :

```twig
{% block upload_widget -%}
    {{-block("hidden_widget") -}}
    <input type="file" class="upload-box-file" data-rel="{{ form.vars.id }}"/>
{%- endblock %}
```
Keep the class and `data-rel` attribute. 

## <a-name="3"></a>Usage

### Step 1 : Use the upload field type

```php
<?php

    namespace App\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use TBoileau\UploadBundle\Form\UploadType;
    
    class FooType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('bar', UploadType::class);
        }
    }
```

You can add options on your field, it' recommanded for more security : 

```php
<?php

    //...
    $builder->add('bar', UploadType::class, [
        "mime_types" => ["image/png", "image/gif", "image/jpeg"],
        "max_size" => "2M",
        "image" => [
            "minRatio" => 4/3,
            "maxRatio" => 16/9,
            "minWidth" => 400,
            "maxWidth" => 800,
            "minHeight" => 300,
            "maxHeight" => 600
        ]
    ]);    
    //...
```

* *mime_types* : [Check this list of MIME types](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Complete_list_of_MIME_types)
* *max_size* : Refere to this regular expression `/^\d+[O|K|M]$/`.
* *image* : You can specify a few specs, like ratio, width and height.

### Step 2 : Add assets to your view or template

```twig
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}Welcome!{% endblock %}</title>
        {% block stylesheets %}
            <link rel="stylesheet" href="{{ asset("bundles/tboileauupload/css/upload.css") }}">
        {% endblock %}
    </head>
    <body>
        {% block body %}{% endblock %}
        {% block javascripts %}
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
            <script src="{{ asset("bundles/tboileauupload/js/upload.js") }}"></script>
        {% endblock %}
    </body>
</html>
```

Don't forget to add jQuery before the upload script.

### Step 3 : Javascript usage

By default, all DOM element with class `upload-box`, however you can call the jQuery custom method :

```javascript
$("seletor").upload();
```

And if you want more control of your widget, you can override the default options :

```javascript
$("seletor").upload({
    texts: {
        error: "An error occured.",
        maxSizeRegex: "Max size is not valid.",
        missingAttributes: "Missing 'image' attributes.",
        tooManyAttributes: "Too many 'image' attributes.",
        noFile : "No file selected.",
        sizeTooBig : "Your file is too big.",
        imgTooBig : "Your image is too big.",
        imgTooSmall : "Your image is too small.",
        imgRatioTooBig : "The ratio of your image is too big.",
        imgRatioTooSmall : "The ratio of your image is too small.",
        mimeTypeError : "Your file is not valid.",
        success: "Cancel and upload an another one ?",
        label: "<strong>Choose a file</strong> or drag it here",
        uploading: "Uploading..."
    },
    onUpload: function(loaded, total) {
        var valueNow = Math.ceil(loaded / total) * 100;
        console.log(valueNow + "%");
    },
    onSuccess: function(response) {
        $input.val(response.file);
    },
    onError: function(response) {
        defaults.texts[response.message];
    }
});
```

* *$input* : It refer to your field that contain the relative path of uploaded file.
* *texts* : It contain all messages (check the validator message key in [File model](src/Model/File.php)).
* *onUpload* : Event called during the upload.
* *onError* : Event called when a error is occured.
* *onSuccess* : Event called when the upload is complete.