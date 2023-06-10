<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Cookie\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Cookie\SameSite;
use Tobento\Service\Cookie\SameSiteInterface;

/**
 * SameSiteTest
 */
class SameSiteTest extends TestCase
{
    public function testConstructMethod()
    {
        $sameSite = new SameSite(value: 'Lax');
        
        $this->assertInstanceof(SameSiteInterface::class, $sameSite);
        $this->assertSame('Lax', $sameSite->value());
    }
    
    public function testValidValue()
    {
        $this->assertSame('Lax', (new SameSite(value: 'Lax'))->value());
        $this->assertSame('Strict', (new SameSite(value: 'Strict'))->value());
        $this->assertSame('None', (new SameSite(value: 'None'))->value());
    }
    
    public function testValidValueAreCaseInsensitive()
    {
        $this->assertSame('lax', (new SameSite(value: 'lax'))->value());
        $this->assertSame('strict', (new SameSite(value: 'strict'))->value());
        $this->assertSame('none', (new SameSite(value: 'none'))->value());
    }
    
    public function testInvalidValueFallbackToDefault()
    {
        $this->assertSame('Lax', (new SameSite(value: 'foo'))->value());
    }
    
    public function testWithValueMethod()
    {
        $sameSite = new SameSite(value: 'Lax');
        $sameSiteNew = $sameSite->withValue('Strict');
        
        $this->assertFalse($sameSite === $sameSiteNew);
        $this->assertSame('Strict', $sameSiteNew->value());
        $this->assertSame('Lax', $sameSite->withValue('foo')->value());
    }
    
    public function testValuesMethod()
    {
        $sameSite = new SameSite(value: 'Lax');

        $this->assertSame(['Strict', 'Lax', 'None'], $sameSite->values());
    }
}