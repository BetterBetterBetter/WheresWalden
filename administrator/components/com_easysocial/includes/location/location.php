<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialLocation
{
    static $providers = array();

    protected $provider;

    private $baseProviderClassname = 'SocialLocationProviders';

    public function __construct($provider = null)
    {
        $this->loadProvider($provider);
    }

    public function loadProvider($provider = null)
    {
        // If no provider is given, then we load the default one from the settings
        if (empty($provider)) {
            $provider = FD::config()->get('location.provider', 'fallback');
        }

        $this->provider = $this->getProvider($provider);

        return $this->provider;
    }

    public function getProvider($provider)
    {
        if (!isset(SocialLocation::$providers[$provider])) {
            $file = dirname(__FILE__) . '/providers/' . strtolower($provider) . '.php';

            if (!JFile::exists($file)) {
                $fallback = $this->getProvider('fallback');
                $fallback->setError(JText::sprintf('COM_EASYSOCIAL_LOCATION_PROVIDER_FILE_NOT_FOUND', $provider));
                return $fallback;
            }

            require_once($file);

            $classname = $this->baseProviderClassname . ucfirst($provider);

            if (!class_exists($classname)) {
                $fallback = $this->getProvider('fallback');
                $fallback->setError(JText::sprintf('COM_EASYSOCIAL_LOCATION_PROVIDER_CLASS_NOT_FOUND', $provider));
                return $fallback;
            }

            $class = new $classname;

            // If provider is not a extended class from abstract class, we do not want it
            if (!is_a($class, $this->baseProviderClassname)) {
                $fallback = $this->getProvider('fallback');
                $fallback->setError(JText::sprintf('COM_EASYSOCIAL_LOCATION_PROVIDER_INVALID_CLASS', $provider));
                return $fallback;
            }

            // Now we check if the provider's initialisation generated any errors
            if ($class->hasErrors()){
                $fallback = $this->getProvider('fallback');
                $fallback->setError($class->getError());
                return $fallback;
            }

            SocialLocation::$providers[$provider] = $class;
        }

        return SocialLocation::$providers[$provider];
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->provider, $method), $arguments);
    }
}
