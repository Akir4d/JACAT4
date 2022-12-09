<?php

namespace Config\Emergency;

use CodeIgniter\Config\BaseConfig;

class Emergency extends BaseConfig
{
    /**
     * Either configure both the password and the username within the ".env" file 
     * or the login inside the emergency console will not function. 
     * Both the password and the username must be a hash (just like passwords) 
     * rather than a plain text.
     * @var mixed
     */
    public $login = [
        'firstname' => "Emergency",  
        'lastname' => "Console",  
        'username' => 'username hash goes here or inside .env file',
        'password' => 'password hash goes here or inside .env file'
    ];
}
