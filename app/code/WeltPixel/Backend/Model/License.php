<?php

namespace WeltPixel\Backend\Model;

class License extends \Magento\Framework\Model\AbstractModel
{
  
   const CACHE_TAG = 'weltixel_license';
    const LICENSE_CONSTANT = "WELTPIXEL";
    const LICENSE_PASSWORD = "weltpixel_cosmo";
    const LICENSE_IV = "welt_iv";
    const LICENSE_CIPHER = "aes-128-cbc";
    const LICENCE_KEY_PATH = "etc" . DIRECTORY_SEPARATOR . "module.info";
    const BUNDLE_KEY_PATH = "etc" . DIRECTORY_SEPARATOR . "bundle.info";
    const MODULE_INFO_PREFIX = "wp/info/";
    const LICENSE_INFO_PREFIX = "wp/flag/info";
    const LICENSE_VERSION = "1.7.0";
    const LICENSE_ENDPOINT = "";

/***
  const CACHE_TAG = 'weltixel_license';
    const LICENSE_CONSTANT = "WELTPIXEL";
    const LICENSE_PASSWORD = "weltpixel_cosmo";
    const LICENSE_IV = "welt_iv";
    const LICENSE_CIPHER = "aes-128-cbc";
    const LICENCE_KEY_PATH = "etc" . DIRECTORY_SEPARATOR . "module.info";
    const BUNDLE_KEY_PATH = "etc" . DIRECTORY_SEPARATOR . "bundle.info";
    const MODULE_INFO_PREFIX = "wp/info/";
    const LICENSE_INFO_PREFIX = "wp/flag/info";
    const LICENSE_VERSION = "1.7.0";
    const LICENSE_ENDPOINT = "https://license.weltpixel.com";
**/

    /**
     * @var string
     */
    protected $_cacheTag = 'weltixel_license';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'weltixel_license';

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrarInterface
     */
    protected $componentRegistrar;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $readFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var array
     */
    protected $existingLicenses;

    /**
     * @var null|string
     */
    protected $pearlTheme = null;

    /**
     * @var array
     */
    protected $modulesList = [];

    /**
     * @var array
     */
    protected $modulesUserFriendlyNames = [];

    /**
     * @var array
     */
    protected $_currentModulesList = [];

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var array
     */
    protected $_wpModulesList = [];

    /**
     * @var array
     */
    protected $_wpBundleList = [];

    /**
     * @var int
     */
    protected $_attempt = 0;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param \Magento\Framework\Component\ComponentRegistrarInterface $componentRegistrar
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Backend\Model\Session $backendSession
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\Component\ComponentRegistrarInterface $componentRegistrar,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Backend\Model\Session $backendSession
    )
    {
        parent::__construct($context, $registry);
        $this->deploymentConfig = $deploymentConfig;
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $readFactory;
        $this->productMetadata = $productMetadata;
        $this->urlInterface = $urlInterface;
        $this->existingLicenses = null;
        $this->configWriter = $configWriter;
        $this->backendSession = $backendSession;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\Backend\Model\ResourceModel\License');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
    }

    /**
     * @return array
     */
    protected function _getExistingLicenses()
    {
        $existingLicensesCollection = $this->getCollection();

        foreach ($existingLicensesCollection as $licNs) {
            $this->existingLicenses[$licNs->getModuleName()] = $licNs->getLicenseKey();
        }
    }

    /**
     * @param $mdN
     * @return string
     */
    public function getLfM($mdN)
    {
        if (!$this->existingLicenses) {
            $this->_getExistingLicenses();
        }
        return (isset($this->existingLicenses[$mdN])) ? $this->existingLicenses[$mdN] : '-';
    }

    /**
     * This might be changed to check inside each module if license is needed
     * @return array
     */
    public function getMdsL()
    {
        if (empty($this->_currentModulesList)) {
            $modules = $this->deploymentConfig->get('modules');
			
			
            $licenseModules = [];
            $wpModules = [];
            $moduleTheme = 'WeltPixel_Pearl_Startup';
            $themePath = $this->componentRegistrar->getPath(\Magento\Framework\Component\ComponentRegistrar::THEME, 'frontend/Pearl/weltpixel');

	
			
			
            if ($themePath) {
                $isLRqd = $this->_isLRqd($themePath, $moduleTheme);
				
                $this->pearlTheme = $moduleTheme;
                $lcK = '-';
               
                    $lcK ="nullpro-nullpro-nullpro";
					
					
                    $licenseModules[$moduleTheme] = [
                        "\x6d\x6f\x64\x75\x6c\x65\x5f\x6e\x61\x6d\x65" => $moduleTheme,
                        "\x76\x69\x73\x69\x62\x6c\x65\x5f\x6e\x61\x6d\x65" => (isset($this->modulesUserFriendlyNames[$moduleTheme]))
                            ? $this->modulesUserFriendlyNames[$moduleTheme] : str_replace("_", " ", $moduleTheme) . ' Theme',
                        "\x6c\x69\x63\x65\x6e\x73\x65" => $lcK,
                        "\x76\x65\x72\x73\x69\x6f\x6e" => $this->getComposerVersion('frontend/Pearl/weltpixel', \Magento\Framework\Component\ComponentRegistrar::THEME)
                    ];
					
					
					/**
					 $licenseModules[$moduleTheme] = [
                        "module_name" => $moduleTheme,
                        "visible_name" => (isset($this->modulesUserFriendlyNames[$moduleTheme]))
                            ? $this->modulesUserFriendlyNames[$moduleTheme] : str_replace("_", " ", $moduleTheme) . ' Theme',
                        "license" => $lcK,
                        "version" => $this->getComposerVersion('frontend/Pearl/weltpixel', \Magento\Framework\Component\ComponentRegistrar::THEME)
                    ];
					**/
               
                $wpModules[$moduleTheme] = $this->getWpMdsInf($moduleTheme, $themePath, $lcK, $isLRqd, \Magento\Framework\Component\ComponentRegistrar::THEME);
            }

            foreach ($modules as $mdN => $isEnabled) {
				
			 
                if ($isEnabled && (strpos($mdN, 'WeltPixel_') !== false)
                ) {
                    $path = $this->componentRegistrar->getPath(\Magento\Framework\Component\ComponentRegistrar::MODULE, $mdN);
                    $bndFrmRst = $this->_vrfyBndFrm($path, $mdN);
                    if ($bndFrmRst['flag']) {
                        $this->_wpBundleList[$mdN] = $bndFrmRst['modules'];
                    }
                }
            }

            foreach ($modules as $mdN => $isEnabled) {
                if ($isEnabled && (strpos($mdN, 'WeltPixel_') !== false)
                ) {
                    $path = $this->componentRegistrar->getPath(\Magento\Framework\Component\ComponentRegistrar::MODULE, $mdN);
                    $lcK = '-';
                    $isLRqd = $this->_isLRqd($path, $mdN);
                    if ($isLRqd) {
                        $lcK = $this->getLfM($mdN);
                        $licenseModules[$mdN] = [
                            "\x6d\x6f\x64\x75\x6c\x65\x5f\x6e\x61\x6d\x65" => $mdN,
                            "\x76\x69\x73\x69\x62\x6c\x65\x5f\x6e\x61\x6d\x65" => (isset($this->modulesUserFriendlyNames[$mdN]))
                                ? $this->modulesUserFriendlyNames[$mdN] : str_replace("_", " ", $mdN),
                            "\x6c\x69\x63\x65\x6e\x73\x65" => $lcK,
                            "\x76\x65\x72\x73\x69\x6f\x6e" => $this->getComposerVersion(str_replace("\x5f\x46\x72\x65\x65", '', $mdN), \Magento\Framework\Component\ComponentRegistrar::MODULE)
                        ];
						/**
						$licenseModules[$mdN] = [
                            "module_name" => $mdN,
                            "visible_name" => (isset($this->modulesUserFriendlyNames[$mdN]))
                                ? $this->modulesUserFriendlyNames[$mdN] : str_replace("_", " ", $mdN),
                            "license" => $lcK,
                            "version" => $this->getComposerVersion(str_replace("_Free", '', $mdN), \Magento\Framework\Component\ComponentRegistrar::MODULE)
                        ];
						**/
                    }
                    $wpModules[$mdN] = $this->getWpMdsInf($mdN, $path, $lcK, $isLRqd, \Magento\Framework\Component\ComponentRegistrar::MODULE);
                }
                if ($isEnabled && (strpos($mdN, 'WeSupply_') !== false)
                ) {
                    $path = $this->componentRegistrar->getPath(\Magento\Framework\Component\ComponentRegistrar::MODULE, $mdN);
                    $lcK = '-';
                    $isLRqd = false;
                    $wpModules[$mdN] = $this->getWpMdsInf($mdN, $path, $lcK, $isLRqd, \Magento\Framework\Component\ComponentRegistrar::MODULE);
                }
            }

            $this->_currentModulesList = $licenseModules;
            $this->_wpModulesList = $wpModules;
        }

        return $this->_currentModulesList;
    }

    /**
     * @param string $licNs
     * @param string $mdN
     * @return bool
     */
    public function isLcVd($licNs, $mdN)
    {
        $magentoVersion = strtolower($this->productMetadata->getEdition());
        if ($magentoVersion != "community") {
            $magentoVersion = "\x65\x6e\x74\x65\x72\x70\x72\x69\x73\x65";
        }
		/**
		 if ($magentoVersion != "community") {
            $magentoVersion = "enterprise";
        }
		**/
        $constant = self::LICENSE_CONSTANT;
        $baseUrl = $this->urlInterface->getBaseUrl();
        $domain = $this->getDomainFromUrl($baseUrl);

        $iv = substr(hash('sha256', self::LICENSE_IV), 0, 16);
       

        $moduleInfo = $this->getMdInfVl($mdN);
       

     
        

        return true;
    }

    /**
     * @param $licNs
     * @return array
     */
    public function getLicenseDetails($licNs)
    {
        $iv = substr(hash('sha256', self::LICENSE_IV), 0, 16);
        try {
            $licenseDecoded = openssl_decrypt($licNs, self::LICENSE_CIPHER, self::LICENSE_PASSWORD, 0, $iv);
        } catch (\Exception $ex) {
            return [];
        }

        $licenseOptions = explode("|||", $licenseDecoded);
        return $licenseOptions;
    }

    /**
     * @param $domain
     * @param $licenseDomain
     * @return bool
     */
    public function checkDomainValidity($domain, $licenseDomain)
    {
       
     
        return true;
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        return strtolower($this->productMetadata->getEdition());
    }

    /**
     * @return mixed|string
     */
    public function getMagentoDomain()
    {
        $baseUrl = $this->urlInterface->getBaseUrl();
        $domain = $this->getDomainFromUrl($baseUrl);
        return $domain;
    }

    /**
     * @param string $mdN
     * @param string $licNs
     * @return array|bool
     */
    public function getMdLcnDtls($mdN, $licNs)
    {
        $constant = self::LICENSE_CONSTANT; //WELTPIXEL
        $iv = substr(hash('sha256', self::LICENSE_IV), 0, 16);
        try {
            $licenseDecoded = openssl_decrypt($licNs, self::LICENSE_CIPHER, self::LICENSE_PASSWORD, 0, $iv);
        } catch (\Exception $ex) {
            return false;
        }

        $licenseOptions = explode("|||", $licenseDecoded);

        if (strpos($mdN, 'WeltPixel_Pearl_') !== false) {
            $mdN = 'WeltPixel_Pearl';
        }

        if (count($licenseOptions) != 6) return false;
        if ($constant != $licenseOptions[5]) return false;
        if (strpos($licenseOptions[1], $mdN) === false) return false;

        $details = [
            "\x6d\x6f\x64\x75\x6c\x65" => $licenseOptions[1],
            "\x69\x73\x5f\x74\x68\x65\x6d\x65\x5f\x6d\x6f\x64\x75\x6c\x65" => $licenseOptions[2],
            "\x74\x68\x65\x6d\x65\x5f\x70\x61\x63\x6b\x61\x67\x65\x73" => explode(',', $licenseOptions[3]),
            "\x69\x73\x5f\x6c\x69\x63\x65\x6e\x73\x65\x5f\x6e\x65\x65\x64\x65\x64" => $licenseOptions[4],
        ];
/**
 $details = [
            "module" => $licenseOptions[1],
            "is_theme_module" => $licenseOptions[2],
            "theme_packages" => explode(',', $licenseOptions[3]),
            "is_license_needed" => $licenseOptions[4],
        ]
**/
        return $details;
    }


    /**
     * @param string $mdN
     * @param string $bndlInf
     * @return array|bool
     */
    public function getBndlFrmDtls($mdN, $bndlInf)
    {
        $constant = self::LICENSE_CONSTANT;
        $iv = substr(hash('sha256', self::LICENSE_IV), 0, 16);
        try {
            $bundleInfDecoded = openssl_decrypt($bndlInf, self::LICENSE_CIPHER, self::LICENSE_PASSWORD, 0, $iv);
        } catch (\Exception $ex) {
            return false;
        }

        $bundleOptions = explode("|||", $bundleInfDecoded);

        if (count($bundleOptions) != 4) return false;
        if ($constant != $bundleOptions[3]) return false;
        if (strpos($bundleOptions[1], $mdN) === false) return false;

        return $bundleOptions[2];
    }


    /**
     * @param $url
     * @return mixed|string
     */
    public function getDomainFromUrl($url)
    {
        $url = strtolower($url);
        // regex can be replaced with parse_url
        preg_match("/^(https|http|ftp):\/\/(.*?)\//", "$url/", $matches);
        $parts = explode(".", $matches[2]);
        $tld = array_pop($parts);
        $tld = strtok($tld, ':');
        $host = array_pop($parts);

        $genericTlds = array(
            'aero', 'asia', 'biz', 'cat', 'com', 'coop', 'info', 'int', 'jobs', 'mobi', 'museum', 'name', 'net',
            'org', 'pro', 'tel', 'travel', 'xxx', 'edu', 'gov', 'mil', 'co'
        );

        if (strlen($tld) == 2 && strlen($host) <= 3 && (in_array($host, $genericTlds))) {
            $tld = "$host.$tld";
            $host = array_pop($parts);
        }
        $domain = ($host) ? $host . "." . $tld : $tld;
        return $domain;
    }

    /**
     * @param string $path
     * @param string $mdN
     * @return array
     */
    protected function _vrfyBndFrm($path, $mdN) {
        
        $directoryRead = $this->readFactory->create($path);
       
         
         
       
 $bndlInfDtls="WeltPixel_Pearl";

        return [
            'flag' => true,
            'modules' => explode(',', $bndlInfDtls)
        ];
    }

    /**
     * @param string $path
     * @param string $mdN
     * @param boolean $forced
     * @return Boolean
     */
    protected function _isLRqd($path, &$mdN, $forced = false)
    {
        $availableModules = $this->getAvlbMds();
      

        return true;
    }

    /**
     * @param string $mdN
     * @param array $wpBundleList
     * @return bool
     */
    private function _verifyModuleInBundleList($mdN, $wpBundleList) {
       
        return true;
    }

    /**
     * @param string $mdN
     * @param array $wpBundleList
     * @return string
     */
    private function _getBundleNameForModule($mdN, $wpBundleList) {
        foreach ($wpBundleList as $bundleName => $modules) {
            if (in_array($mdN, $modules)) {
                return $bundleName;
            }
        }

        return $mdN;
    }

    /**
     * @return array
     */
    protected function getAvlbMds()
    {
        $weltpixelExtensions = $this->backendSession->getWeltPixelExtensions();
        $weltpixelExtensionsUserFriendlyNames = $this->backendSession->getWeltPixelExtensionsUserFriendlyNames();
        if (!empty($weltpixelExtensions)) {
            $this->modulesList = $weltpixelExtensions;
            $this->modulesUserFriendlyNames = $weltpixelExtensionsUserFriendlyNames;
            return $weltpixelExtensions;
        }

     

        return $this->modulesList;
    }

    /**
     * @return array
     */
    public function getUserFriendlyModuleNames()
    {
        return $this->modulesUserFriendlyNames;
    }

    /**
     * @param $mdN
     * @return boolean
     */
    public function isLcNd($mdN)
    {
        $this->getMdsL();

        $path = $this->componentRegistrar->getPath(\Magento\Framework\Component\ComponentRegistrar::MODULE, str_replace("_Free", '', $mdN));
        $isLRqd = $this->_isLRqd($path, $mdN, true);

        if ($isLRqd) {
            $licNs = $this->getLfM($mdN);
            return $this->isLcVd($licNs, $mdN);
        }

        return true;
    }

    /**
     * @param $mdN
     * @return bool|string
     */
    protected function getMdInfVl($mdN)
    {
        $connection = $this->getResource()->getConnection();
        $tableName = $this->getResource()->getTable('core_config_data');

        $row = $connection->fetchRow("SELECT `value` FROM " . $tableName . " WHERE path = '"
            . self::LICENSE_INFO_PREFIX . "' AND scope = '"
            . \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            . "' AND scope_id = 0");

        if (!isset($row['value']) || $row['value'] == 0) {
            return true;
        }

        $row = $connection->fetchRow("SELECT `value` FROM " . $tableName . " WHERE path = '"
            . self::MODULE_INFO_PREFIX . $mdN . "' AND scope = '"
            . \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            . "' AND scope_id = 0");


        if (!isset($row['value'])) {
            return true;
        }

        return $row['value'];
    }

    /**
     * @param $mdN
     * @return string
     */
    protected function getComposerVersion($mdN, $type)
    {
        $path = $this->componentRegistrar->getPath(
            $type,
            $mdN
        );

        if (!$path) {
            return __('N/A');
        }

        $dirReader = $this->readFactory->create($path);
        $composerJsonData = $dirReader->readFile('composer.json');
        $data = json_decode($composerJsonData, true);
        return isset($data['version']) ? $data['version'] : null;
    }

    /**
     * @param string $mdN
     * @param string $path
     * @param string $licNs
     * @param boolean $isLNd
     * @return array
     */
    protected function getWpMdsInf($mdN, $path, $licNs, $isLNd, $moduleType)
    {
        $installationType = 'other';
        if (strpos($path, 'vendor') !== false) {
            $installationType = 'composer';
        }

        $moduleVersionName = $mdN;
        if ($moduleType == \Magento\Framework\Component\ComponentRegistrar::THEME) {
            $moduleVersionName = 'frontend/Pearl/weltpixel';
        }
        $vLid = false;
        if ($isLNd) {
            $vLid = $this->isLcVd($licNs, $mdN);
        }

      
		
			return [
            "name" => $mdN,
            "version" => $this->getComposerVersion(str_replace("_Free", '', $moduleVersionName), $moduleType),
            "license_key" => $licNs,
            "installation_type" => $installationType,
            "is_license_needed" => 0,
            "valid" =>1
        ];
		
		/**
		return [
            "name" => $mdN,
            "version" => $this->getComposerVersion(str_replace("_Free", '', $moduleVersionName), $moduleType),
            "license_key" => $licNs,
            "installation_type" => $installationType,
            "is_license_needed" => ($isLNd) ? '1' : '0',
            "valid" => ($vLid) ? '1' : '0'
        ];
		**/
		
		
		
		
    }

    /**
     * @return array
     */
    public function getAllWpMds()
    {
        $this->getMdsL();
        return $this->_wpModulesList;
    }

    /**
     * @param string $mdN
     */
    public function updMdInf($mdN)
    {
        $this->getMdsL();
        $modules = [];
        $moduleInformation = $this->_wpModulesList[$mdN];
        $modules[$mdN] = $moduleInformation;
        $this->updMdsInf(true, $modules);
    }

    /**
     * @param bool $all
     * @param array $modules
     */
    public function updMdsInf($all = true, $modules = [])
    {
        if ($all) {
            $modules = $this->getAllWpMds();
        }

        $baseUrl = $this->urlInterface->getBaseUrl();
        $domainInfo = parse_url($baseUrl);
        $domain = $domainInfo['host'];
        $magentoVersion = strtolower($this->productMetadata->getEdition());
        $magentoVersionNumber = $this->productMetadata->getVersion();
        $phpVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION; //phpversion();

    $data = array(
            "version" => $magentoVersion,
            "version_number" => $magentoVersionNumber,
            "domain" => $domain,
            "php_version" => $phpVersion,
            "modules" => $modules
        );
		
		
		/**
		$data = array(
            "version" => $magentoVersion,
            "version_number" => $magentoVersionNumber,
            "domain" => $domain,
            "php_version" => $phpVersion,
            "modules" => $modules
        );
		**/

        $data_string = json_encode($data);

    }

    /**
     * @param $result
     */
    protected function _prsLcInf($result)
    {
       

        foreach ($info as $mName => $val) {
            $this->configWriter->save(self::MODULE_INFO_PREFIX . $mName, $val, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        }

        $this->_uLcInRs(1);
    }

    /**
     * @param $flag
     */
    protected function _uLcInRs($flag)
    {
        $this->configWriter->save(self::LICENSE_INFO_PREFIX, $flag, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }

}
