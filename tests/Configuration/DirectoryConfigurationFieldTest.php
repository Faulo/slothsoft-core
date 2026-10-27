<?php
declare(strict_types = 1);

namespace Slothsoft\Core\Configuration;

use PHPUnit\Framework\TestCase;
use BadMethodCallException;

/**
 * DirectoryConfigurationFieldTest
 *
 * @see DirectoryConfigurationField
 */
final class DirectoryConfigurationFieldTest extends TestCase {
    
    /**
     *
     * @test
     */
    public function testClassExists(): void {
        $this->assertTrue(class_exists(DirectoryConfigurationField::class), "Failed to load class 'Slothsoft\Core\Configuration\DirectoryConfigurationField'!");
    }
    
    /**
     * @dataProvider provideConfigurationMethods
     * @throws \Exception
     */
    public function testDirectoryCreationIsDeferredUntilFirstGetValue(bool $useConstructor): void {
        $blockedParent = temp_file(__METHOD__);
        $this->assertSame(0, file_put_contents($blockedParent, ''));
        $path = $blockedParent . DIRECTORY_SEPARATOR . 'directory';
        
        if ($useConstructor) {
            $field = new DirectoryConfigurationField($path);
        } else {
            $field = new DirectoryConfigurationField();
            $field->setValue($path);
        }
        
        $this->assertTrue($field->hasValue());
        $this->assertFileExists($blockedParent);
        $this->assertDirectoryDoesNotExist($path);
        
        $this->assertTrue(unlink($blockedParent));
        $this->assertDirectoryDoesNotExist($path);
        
        $value = $field->getValue();
        
        $this->assertDirectoryExists($path);
        $this->assertSame(realpath($path), $value);
        $this->assertSame($value, $field->getValue());
    }
    
    public function provideConfigurationMethods(): array {
        return [
            'constructor' => [true],
            'setValue' => [false]
        ];
    }
    
    public function testSetValueRejectsEmptyPath(): void {
        $field = new DirectoryConfigurationField();
        
        $this->expectException(BadMethodCallException::class);
        $field->setValue('');
    }
    
    /**
     * @throws \Exception
     */
    public function testGetValueRejectsFilePath(): void {
        $path = temp_file(__METHOD__);
        $this->assertSame(0, file_put_contents($path, ''));
        $field = new DirectoryConfigurationField($path);
        
        $this->expectException(BadMethodCallException::class);
        $field->getValue();
    }
    
    /**
     * @throws \Exception
     */
    public function testSetValueDefersCreationAfterPreviousGetValue(): void {
        $parent = temp_dir(__METHOD__);
        $field = new DirectoryConfigurationField($parent);
        $this->assertSame(realpath($parent), $field->getValue());
        
        $path = $parent . DIRECTORY_SEPARATOR . 'nested' . DIRECTORY_SEPARATOR . 'directory';
        $field->setValue($path);
        $this->assertDirectoryDoesNotExist($path);
        
        $value = $field->getValue();
        $this->assertDirectoryExists($path);
        $this->assertSame(realpath($path), $value);
    }
}
