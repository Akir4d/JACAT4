<?php

namespace App\Controllers\Emergency;
use Throwable;

class Envedit extends Auth
{

    private function testDb(array $config): string|null
    {
        try {
            $db = \Config\Database::connect($config);
            return $db->reconnect();
        } catch (Throwable $e){
            return $e->getMessage();
        }
    }

    public function postDbTest()
    {
        $config = (array) $this->request->getJSON();
        $error = $this->testDb($config);
        return $this->renderJson(["error"=> $error==null?false:true, "message" => $error]);
    }


    public function postSaveDbDefault(){

        $config = (array) $this->request->getJSON();
        // test if configuration Works
        $error = $this->testDb($config);
        if($error !== null) return $this->renderJson(["error"=> true, "message" => $error]);
        $file = file_get_contents(APPPATH . '../.env');
        foreach($config as $key => $value){
            $file = $this->replaceOnEnv('database.default.' . $key, $value, $file);
        }
        file_put_contents(APPPATH . '../.env', $file);
        return $this->renderJson(["error"=> false, "message" => 'Saved!']);
    }

}
