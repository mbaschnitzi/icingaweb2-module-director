<?php

namespace Icinga\Module\Director\Web\Hook;

use Icinga\Module\Director\Web\Form\QuickForm;
use Icinga\Module\Director\Db;

abstract class ImportSourceHook
{
    protected $settings = array();

    public function getName()
    {
        $parts = explode('\\', get_class($this));
        $class = preg_replace('/ImportSource/', '', array_pop($parts));

        if (array_shift($parts) === 'Icinga' && array_shift($parts) === 'Module') {
            $module = array_shift($parts);
            if ($module !== 'Director') {
                if ($class === '') {
                    return sprintf('%s module', $module);
                }
                return sprintf('%s (%s)', $class, $module);
            }
        }

        return $class;
    }

    public static function loadByName($name, $db)
    {
        $db = $db->getDbAdapter();
        $source = $db->fetchRow(
            $db->select()->from(
                'import_source',
                array('id', 'provider_class')
            )->where('source_name = ?', $name)
        );

        $settings = $db->fetchPairs(
            $db->select()->from(
                'import_source_setting',
                array('setting_name', 'setting_value')
            )->where('source_id = ?', $source->id)
        );

        $obj = new $source->provider_class;
        $obj->setSettings($settings);

        return $obj;
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * Returns an array containing importable objects
     *
     * @return array
     */
    abstract public function fetchData();

    /**
     * Returns a list of all available columns
     *
     * @return array
     */
    abstract public function listColumns();

    /**
     * Override this method if you want to extend the settings form
     *
     * @param  QuickForm $form QuickForm that should be extended
     * @return QuickForm
     */
    public static function addSettingsFormFields(QuickForm $form)
    {
        return $form;
    }
}
