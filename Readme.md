# ContaoEloquentBundle

This bundle depends on the great [eloquent-bundle](https://github.com/WouterJ/WouterJEloquentBundle) from wouterj which
makes eloquent usable in symfony. We want to go a step further and integrate Eloquent in [Contao](https://contao.org/).

At the moment this bundle implements
- hasMany, hasOne, belongsTo, belongsToMany relations
- command to create dca-files for pivot tables
- PublishedScopeTrait (to query only published models)
- ContaoFilesModelTrait (possibility to return a contao file model instead of just the uuid)
- ContaoModelConsumerTrait (turns contao models into eloquent models)

More features which will be implemented:
- hasManyThrough
- morphTo
- morphMany
- morphToMany
- morphedByMany
- Eloquent models for all core Contao models

## Guide

### 1. Install the Bundle

```bash
composer require fuzzyma/contao-eloquent-bundle
```

### 2. Activate the Bundle

In your AppKernel.php add:

```php
public function registerBundles() {

    $bundles = [

        //...
        new WouterJ\EloquentBundle\WouterJEloquentBundle(), // we depend on it so we have to load it
        new Fuzzyma\Contao\EloquentBundle\ContaoEloquentBundle() // and that's ours

    ];

}
```

This bundles are now active. But we have to configure the eloquent environment.
You can see how it works here: [eloquent-bundle](https://github.com/WouterJ/WouterJEloquentBundle).
But for simplification here is the short version:

### 3. Configure Eloquent

```yaml
wouterj_eloquent:
    connections:
        default:
            database:  "%database_name%"
            driver:    mysql
            host:      "%database_host%"
            username:  "%database_user%"
            password:  "%database_password%"
            charset:   utf8
            collation: utf8_unicode_ci
            prefix:    'tl_'
    default_connection: default
    eloquent: true
    aliases: false
```

After this is done we can finally use it

## Usage

Say we have a `Topic` model and a `Tag` model. Every `Topic` can have multiple `Tag`s and the other way round.
To use Eloquents relation we need a pivot table but contao does not understand pivot tables. Furthermore we want
a multiple select field for topics and tags which works just fine. We can do that:

```php

/**
 * Table tl_topics
 */
$GLOBALS['TL_DCA']['tl_topics'] = array
(
    'config' => array
    (
        // all the config
        'model' => Fuzzyma\Contao\EloquentExampleProjectBundle\Models\Topic::class
    )
	// Fields
	'fields' => array
	(
         // id, tstamp and so on and:
        'tags' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_topics']['tags'],
            'inputType' => 'select',
            'eval' => ['multiple' => true],
            'eloquent' => [
                'relation' => 'belongsToMany',
                'model' => Fuzzyma\Contao\EloquentExampleProjectBundle\Models\Tag::class,
                'label' => '%name% [%comment%]'
                // 'medthod' => 'tags' // is automatically assumed
            ]
        ),
	)
);
```

The important part is the model key in the config section and the eloquent key in the tags section.
*Note:* Make sure, that the field-name is the same as defined in the Model. So your Topic Model would look like this:

```php
namespace NamespaceToBundle\Models;

use Illuminate\Database\Eloquent\Model;
use Fuzzyma\Contao\EloquentBundle\Traits\ContaoFilesModelTrait; // only needed if you deal with files

class Topic extends Model
{

    use ContaoFilesModelTrait;

     // laravel use plural of class name as table. So in case you don't match the convention you can change it here
    protected $table = "topics";
    protected $guarded = [];
    public $timestamps = false; // we don't have timestamps but we could add them if we want

    // that method name must match the field name (without _id) or the specified method!!
    public function tags(){
        return $this->belongsToMany(Tag::class, 'tag_topic'); // you may want to name the pivot table here
    }

    // here the ContaoFilesModelTrait comes into place.
    // The Mutator will convert a call to $topic->thumbnail to a contao file model
    // of course that only works if you defined a field thumbnail in the dca
    public function getThumbnailAttribute($uuid){
        return $this->getContaoFile($uuid);
    }

    // same for setting a new file. Pass a FileModel, an UUID string or just binary
    public function setThumbnailAttribute($file){
        return $this->setContaoFile($file);
    }

}
```

For further code and other relations see the [contao-eloquent-example-project-bundle](https://github.com/Fuzzyma/contao-eloquent-example-project-bundle)

## Generator

This bundle comes shipped with a command to create the dca file for you which then creates the pivot table.
Just run the following command:
```bash
php app/console contao:make:pivot
```

For further options run
```bash
php app/console contao:make:pivot --help
```