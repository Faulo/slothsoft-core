<?php
declare(strict_types = 1);

namespace Slothsoft\Core\DBMS;

/**
 * @deprecated Included for historical compatibility only. The DBMS API is out of support and should not be used in new code.
 */
final class Authority {
    
    public string $server;
    
    public string $user;
    
    public string $password;
    
    public function __construct(string $server, string $user, string $password) {
        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
    }
}
