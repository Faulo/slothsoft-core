<?php
declare(strict_types = 1);
namespace Slothsoft\Core\DBMS;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\Configuration\ConfigurationRequiredException;

/**
 * ClientTest
 *
 * @see Client
 */
class ClientTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Client::class), "Failed to load class 'Slothsoft\Core\DBMS\Client'!");
    }
    
    public function testConfigurationRequired() {
        Client::clearDefaultAuthority();
        
        putenv(Client::ENV_CONNECTION_SERVER);
        putenv(Client::ENV_CONNECTION_USER);
        putenv(Client::ENV_CONNECTION_PASSWORD);
        putenv(Client::ENV_CONNECTION_PASSWORD_FILE);
        
        $this->expectException(ConfigurationRequiredException::class);
        
        Client::getDefaultAuthority();
    }
    
    public function testEnvironmentVariable() {
        Client::clearDefaultAuthority();
        
        putenv(Client::ENV_CONNECTION_SERVER . '=test-server');
        putenv(Client::ENV_CONNECTION_USER . '=test-user');
        putenv(Client::ENV_CONNECTION_PASSWORD . '=test-password');
        putenv(Client::ENV_CONNECTION_PASSWORD_FILE);
        
        $authority = Client::getDefaultAuthority();
        
        $this->assertEquals('test-server', $authority->server);
        
        $this->assertEquals('test-user', $authority->user);
        
        $this->assertEquals('test-password', $authority->password);
    }
    
    public function testEnvironmentVariableFile() {
        Client::clearDefaultAuthority();
        
        putenv(Client::ENV_CONNECTION_SERVER);
        putenv(Client::ENV_CONNECTION_USER);
        putenv(Client::ENV_CONNECTION_PASSWORD);
        putenv(Client::ENV_CONNECTION_PASSWORD_FILE);
        
        $file = temp_file(__CLASS__);
        file_put_contents($file, 'test-password-file');
        putenv(Client::ENV_CONNECTION_PASSWORD_FILE . '=' . $file);
        
        $authority = Client::getDefaultAuthority();
        
        $this->assertEquals('localhost', $authority->server);
        
        $this->assertEquals('root', $authority->user);
        
        $this->assertEquals('test-password-file', $authority->password);
    }
}