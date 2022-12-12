<?php
/**
 * getModuleConfig splits configuration between 
 * to enable it use aop spark config:convert nameOfConfigToConvert
 *  
 * @param mixed $moduleName
 * @return string
 */
function getModuleConfig($moduleName){
    $moduleName = ucfirst(strtolower($moduleName)) .'.php';
    if(isset($_SERVER) && !empty($_SERVER['REQUEST_URI'])){
        $rbase = (function () {
            $vv = explode('/', $_SERVER['SCRIPT_NAME']);
            array_pop($vv);
            return implode('/',$vv);})();

        return (function () use ($rbase, $moduleName) {
            $vv = explode('/', trim(str_replace($rbase, '', $_SERVER['REQUEST_URI']), '/'));
            if(!empty($vv[0])) {
                $module = ucfirst(strtolower($vv[0]));
                $file = APPPATH . 'Config/Modules/' . $module . '/' . $moduleName;
                if(file_exists($file)){
                    return $file;
                } else {
                    return APPPATH . 'Config/Modules/Defaults/' . $moduleName;
                }
            } else {
                return APPPATH . 'Config/Modules/Defaults/' . $moduleName;
            }
         })();
    } else {
        return APPPATH . 'Config/Modules/Defaults/' . $moduleName;
    }
}
