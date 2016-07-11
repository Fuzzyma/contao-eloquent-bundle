<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 06.07.16 17:55
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Hooks;


class DCAEloquentifier
{

    public function parseDCA($strTable)
    {

        if (!is_array($GLOBALS['TL_DCA'][$strTable]['fields'])) return;

        $eloquentFields = array_filter($GLOBALS['TL_DCA'][$strTable]['fields'], function ($field) {
            return isset($field['eloquent']);
        });


        foreach ($eloquentFields as $fieldName => $field) {

            if (!is_array($GLOBALS['TL_DCA'][$strTable]['fields'][$fieldName]['eloquent'])) {
                throw new \InvalidArgumentException(
                    "eloquent has to be of type Array e.g. [\n\t" .
                        "relation => 'hasMany',\n\t" .
                        "model => \\Foo\\Bar\\MyModel::class,\n\t" .
                        "label => '%name%'\n" .
                    "]"
                );
            }

            switch ($field['inputType']) {
                /** @noinspection PhpMissingBreakStatementInspection */
                case 'select':
                    $field['options_callback'] = self::createOptionsCallback($strTable, $fieldName);
                    $field['eval']['includeBlankOption'] = true;
                    // no break!
                default:
                    $field['load_callback'] = self::createLoadCallback($strTable, $fieldName);
                    $field['save_callback'] = self::createSaveCallback($strTable, $fieldName);
                    $field['eval']['doNotSaveEmpty'] = true;
                    break;

            }

            $GLOBALS['TL_DCA'][$strTable]['fields'][$fieldName] = $field;

        }


    }

    // returns the optionCallback needed by contao. e.g.
    //'options_callback' => \VlipgoBundle\Hooks\DCAEloquentifier::createOptionsCallback('tl_content', 'type')
    public static function createOptionsCallback($strTable, $fieldName)
    {

        return function ($dc) use ($strTable, $fieldName) {

            $eloquent = $GLOBALS['TL_DCA'][$strTable]['fields'][$fieldName]['eloquent'];

            // get all possible values from our relation
            $collection = $eloquent['model']::orderBy('id', 'ASC')->get();

            // Build our options array
            return array_map(
                function ($arr) use ($eloquent) {

                    // Replace the placeholder in the label with values from the model
                    $label = preg_replace_callback('/%(\w+)%/', function ($match) use ($arr) {

                        // when there is no %key% in model, we return an empty string
                        if (isset($arr[$match[1]])) {
                            return $arr[$match[1]];
                        } else {
                            return '';
                        }

                    }, $eloquent['label']);

                    // if the label is empty we display at least the id
                    return $label ? : $arr['id'];

                }, $collection->keyBy('id')->toArray());

        };

    }

    public static function createLoadCallback($strTable, $fieldName)
    {

        return [function ($varValue, $dc) use ($strTable, $fieldName) {

            // pull eloquent information out of dca
            $eloquent = $GLOBALS['TL_DCA'][$strTable]['fields'][$fieldName]['eloquent'];

            // creates method name for relation
            $relation = DCAEloquentifier::relationFromField($fieldName, @$eloquent['method']);

            // get relation from our model
            $collection = $GLOBALS['TL_DCA'][$strTable]['config']['model']::with($relation)->find($dc->id)->$relation;

            if (!$collection) {
                return null;
            }

            // return one or multiple ids
            if ($eloquent['relation'] == 'hasOne' || $eloquent['relation'] == 'belongsTo') {
                return $collection->id;
            } elseif ($eloquent['relation'] == 'hasMany' || $eloquent['relation'] == 'belongsToMany') {
                return $collection->pluck('id')->toArray();
            }

            return null;

        }];

    }

    public static function createSaveCallback($strTable, $fieldName)
    {

        return [function ($varValue, $dc) use ($strTable, $fieldName) {

            // pull eloquent information out of dca
            $eloquent = $GLOBALS['TL_DCA'][$strTable]['fields'][$fieldName]['eloquent'];

            // creates method name for relation
            $relation = DCAEloquentifier::relationFromField($fieldName, @$eloquent['method']);

            $delete = @((bool)$eloquent['deleteRelations']);

            // get model from database
            $model = $GLOBALS['TL_DCA'][$strTable]['config']['model']::find($dc->id);

            // in case of hasOne, we set the given relation on the model
            // in case no related model was selected, we remove any possible relation
            if ($eloquent['relation'] == 'hasOne') {

                // get the other relationship from the database and release it / delete it
                if ($model->$relation) {

                    $foreignKey = $model->$relation()->getPlainForeignKey();

                    if ($delete) {
                        // only delete the model, when it is not the same which we want to save in the next step
                        $eloquent['model']::where($foreignKey, $model->id)->where('id', '<>', $varValue ? : 0)->delete();
                        //$model->$relation()->where('id', '<>', $varValue?:0)->delete();
                    } else {
                        // make sure that no other related model points to $model anymore
                        //$model->$relation()->update([$foreignKey => null]);
                        $eloquent['model']::where($foreignKey, $model->id)->update([$foreignKey => null]);
                    }
                }

                // set the new relation
                if ($varValue) {
                    $model->$relation()->save($eloquent['model']::find($varValue));
                }

            }
            // same goes for belongsTo
            // note: belongsTo will never delete its parent model!!
            elseif ($eloquent['relation'] == 'belongsTo') {
                if ($varValue) {
                    $model->$relation()->associate($eloquent['model']::find((int)$varValue));
                    $model->save();
                } else {
                    $model->$relation()->dissociate();
                    $model->save();
                }
            } /**
             * hasMany will be saved dependent on the input.
             * case array of models:
             * create models and save them on $model as relation
             *
             * case array of ids:
             * sync ids with $model
             *
             * If nothing is given, we remove any model related to $model
             */
            elseif ($eloquent['relation'] == 'hasMany') {

                // contao gives us a serialized array
                $varValue = \StringUtil::deserialize($varValue, true);

                // we got multiple arrays as input. cool thing
                if (@is_array($varValue[0])) {

                    // extract ids if present
                    $ids = [];
                    if (isset($varValue[0]['id'])) {
                        $ids = collect($varValue)->pluck('id');
                    }

                    // remove / delete all other models from this relation:
                    if ($delete) {
                        $model->$relation()->whereNotIn('id', $ids)->delete();
                    } else {
                        $foreignKey = $model->$relation()->getPlainForeignKey();
                        $model->$relation()->whereNotIn('id', $ids)->update([$foreignKey => null]);
                    }

                    // create or update models based on the array given
                    foreach ($varValue as $attributes) {
                        $id = 0;
                        if (isset($attributes['id'])) {
                            $id = $attributes['id'];
                            unset($attributes['id']);
                        }

                        // save updated/new model in database
                        $model->$relation()->updateOrCreate(['id' => $id], $attributes);
                    }

                } // this array is flat. It has to be an array of ids
                else {

                    // delete all models which are NOT in the array of ids
                    if ($delete) {
                        $model->$relation()->whereNotIn('id', $varValue)->delete();
                    } else {
                        $foreignKey = $model->$relation()->getPlainForeignKey();
                        $model->$relation()->update([$foreignKey => null]);
                    }

                    // get all related models with that id
                    $related = $eloquent['model']::find($varValue);

                    // and assign them to this model
                    $model->$relation()->saveMany($related);

                    return null;

                }

            } else {

                // update manyToMany Relationship
                $ids = \StringUtil::deserialize($varValue, true);
                $model->$relation()->sync($ids);
            }

            return null;

        }];

    }

    /**
     * Consider this as a bit of magic
     * This method will return a "guessed" method name which is used from the relation
     * Since there is no way in Eloquent to "read" the relations from a model, we have to work with what we get
     *
     * This method only does something when no method was set in the dca
     * It takes one $field of the dca and removes the _id
     * So 'topic_id' will be 'topic' and 'topic_ids' will be 'topics'.
     * If no _id is present, it just uses the $fieldName as method name for the relation
     */
    public static function relationFromField($fieldName, $method = null)
    {
        if ($method) return $method;

        if (substr($fieldName, -3) == '_id') {
            return substr($fieldName, 0, -3);
        }

        if (substr($fieldName, -4) == '_ids') {
            return substr($fieldName, 0, -4) . 's';
        }

        return $fieldName;
    }


} 